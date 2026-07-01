<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // تسجيل الدخول
    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()
            ->with('step', 'login')
            ->withErrors(['email' => 'بيانات الدخول غير صحيحة.',])
            ->onlyInput('email');
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');
    }

    public function updateRole(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح لك بتغيير الصلاحيات.');
        }

        $request->validate([
            'role' => 'required|in:admin,user',
        ]);

        $user->update([
            'role' => $request->role,
        ]);

        return back()->with('success', 'تم تحديث صلاحية المستخدم بنجاح.');
    }

    public function invite(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح لك بإضافة مستخدمين.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $validated['password'] = Hash::make(\Illuminate\Support\Str::random(32));
        $validated['role'] = 'user';
        $validated['phone'] = '';

        User::create($validated);

        return back()->with('success', 'تمت إضافة المستخدم بنجاح. يمكنه استخدام "Forgot password" لتعيين كلمة مروره.');
    }

    // الخطوة 1: إرسال كود مكون من 4 أرقام للإيميل
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->with('step', 'email')
                ->withErrors([
                    'email' => 'لم يتم العثور على هذا البريد الإلكتروني.',
                ])
                ->onlyInput('email');
        }

        $code = rand(1000, 9999);

        $user->update([
            'reset_code' => $code,
            'reset_code_expires_at' => now()->addMinutes(10),
        ]);

        try {
            \Illuminate\Support\Facades\Mail::raw(
                "رمز إعادة تعيين كلمة المرور الخاص بك هو: {$code}\nصالح لمدة 10 دقائق.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('رمز إعادة تعيين كلمة المرور - 6Degrees');
                }
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error(
                'فشل إرسال رمز إعادة التعيين: ' . $e->getMessage()
            );

            return back()
                ->with('step', 'email')
                ->withErrors([
                    'email' => 'حدث خطأ أثناء إرسال البريد، حاول لاحقًا.',
                ])
                ->onlyInput('email');
        }

        return back()
            ->with([
                'step'   => 'code',
                'status' => 'تم إرسال الرمز إلى بريدك الإلكتروني.',
                'email'  => $request->email,
            ]);
    }

    // الخطوة 2: التحقق من الكود
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:4',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->reset_code != $request->code) {
            return back()
                ->with('step', 'code')
                ->with('email', $request->email)
                ->withErrors([
                    'code' => 'الرمز غير صحيح.',
                ]);
        }

        if (now()->greaterThan($user->reset_code_expires_at)) {
            return back()
                ->with('step', 'code')
                ->with('email', $request->email)
                ->withErrors([
                    'code' => 'انتهت صلاحية الرمز، أرسل رمزًا جديدًا.',
                ]);
        }

        return back()
            ->with([
                'step'  => 'password',
                'email' => $request->email,
                'code'  => $request->code,
            ]);
    }

    // الخطوة 3: حفظ كلمة المرور الجديدة
    public function resetPasswordWithCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:4',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (
            !$user ||
            $user->reset_code != $request->code ||
            now()->greaterThan($user->reset_code_expires_at)
        ) {
            return back()
                ->with('step', 'password')
                ->with('email', $request->email)
                ->with('code', $request->code)
                ->withErrors([
                    'password' => 'حدث خطأ، حاول مرة أخرى من البداية.',
                ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_code' => null,
            'reset_code_expires_at' => null,
        ]);

        return redirect('/login')
            ->with('status', 'تم تحديث كلمة المرور بنجاح، يمكنك تسجيل الدخول الآن.');
    }
}
