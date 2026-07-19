<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $email = strtolower(trim($request->email));

        // نحاول نسجل دخول
        if (Auth::attempt(['email' => $email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // نتحقق إذا الإيميل موجود
        $userExists = User::whereRaw('LOWER(email) = ?', [$email])->exists();

        if (!$userExists) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('messages.email_not_found')])
                ->with('step', 'login');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __('messages.invalid_credentials')])
            ->with('step', 'login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

public function sendResetCode(Request $request)
{
$request->validate(['email' => 'required|email']);

$email = strtolower(trim($request->email));

$user = User::whereRaw('LOWER(email) = ?', [$email])->first();

    if (!$user) {
        return redirect('/login')
            ->with('step', 'email')
            ->withErrors(['email' => __('messages.email_not_found')])
            ->withInput(['email' => $request->email]);
    }

    $code = rand(1000, 9999);

    $user->update([
        'reset_code' => $code,
        'reset_code_expires_at' => now()->addMinutes(10),
    ]);

    try {
        \Illuminate\Support\Facades\Mail::raw(
            __('messages.reset_code_mail_body', ['code' => $code]),
            function ($message) use ($user) {
                $message->to($user->email)->subject(__('messages.reset_code_mail_subject'));
            }
        );
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Reset code mail failed: ' . $e->getMessage());

        return redirect('/login')
            ->with('step', 'email')
            ->withErrors(['email' => __('messages.mail_send_error')])
            ->withInput(['email' => $request->email]);
    }

    return redirect('/login')->with([
        'step'   => 'code',
        'status' => __('messages.code_sent'),
      'email' => $email,
    ]);
}

public function verifyResetCode(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'code'  => 'required|digits:4',
    ]);

   $email = strtolower(trim($request->email));

$user = User::whereRaw('LOWER(email) = ?', [$email])->first();

    if (!$user || $user->reset_code != $request->code) {
        return redirect('/login')
            ->with('step', 'code')
            ->with('email', $email)
            ->withErrors(['code' => __('messages.code_incorrect')]);
    }

    if (now()->greaterThan($user->reset_code_expires_at)) {
        return redirect('/login')
            ->with('step', 'code')
            ->with('email', $email)
            ->withErrors(['code' => __('messages.code_expired')]);
    }

    return redirect('/login')->with([
        'step'  => 'password',
        'email' => $email,
        'code'  => $request->code,
    ]);
}
public function resetPasswordWithCode(Request $request)
{
    $request->validate([
        'email'                 => 'required|email',
        'code'                  => 'required',
        'password'              => 'required|confirmed',
    ]);

    $email = strtolower(trim($request->email));

    $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

    if (!$user) {
        return redirect('/login')
            ->withErrors(['email' => __('messages.email_not_found')]);
    }

    if ($user->reset_code != $request->code) {
        return redirect('/login')
            ->with('step', 'password')
            ->with('email', $email)
            ->with('code', $request->code)
            ->withErrors(['password' => __('messages.invalid_code')]);
    }

    $user->password = $request->password;
    $user->reset_code = null;
    $user->reset_code_expires_at = null;
    $user->save();

    return redirect('/login')
        ->with('status', __('messages.password_reset_success'));
}

    public function invite(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $email = strtolower(trim($request->email));
        $password = \Illuminate\Support\Str::random(10);

$user = User::create([
    'name'     => $request->name,
    'email'    => $email,
    'password' => $password,
    'role'     => 'user',
    'phone'    => '',
]);

        Mail::raw(
            "You've been invited to 6Degrees Team Dashboard.\n\nEmail: $email\nPassword: $password\n\nLogin at: " . env('APP_URL') . "/login",
            function ($message) use ($email, $request) {
                $message->to($email)->subject('You\'ve been invited to 6Degrees');
            }
        );

        return redirect()->route('team.dashboard')
            ->with('success', __('messages.invite_sent'));
    }
    
public function updateRole(Request $request, User $user)
{
    if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
        abort(403);
    }

    // حماية super_admin من أي تغيير
    if ($user->role === 'super_admin') {
        return redirect()->route('team.dashboard')
            ->with('error', __('messages.cannot_change_super_admin'));
    }

    // منع تغيير رول نفسك
    if ($user->id === Auth::id()) {
        return redirect()->route('team.dashboard')
            ->with('error', __('messages.no_permission_edit'));
    }

    // بس super_admin يقدر يرفع لـ admin
    if ($request->role === 'admin' && !Auth::user()->isSuperAdmin()) {
        return redirect()->route('team.dashboard')
            ->with('error', __('messages.no_permission_edit'));
    }

    $request->validate(['role' => 'required|in:admin,user']);
    $user->update(['role' => $request->role]);

    return redirect()->route('team.dashboard')
        ->with('success', __('messages.role_updated'));
}



public function apiLogin(Request $request)
{
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (!$token = auth('api')->attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    return response()->json(['token' => $token]);
}

public function apiLogout()
{
    auth('api')->logout();
    return response()->json(['message' => 'Logged out']);
}

public function me()
{
    return response()->json(auth('api')->user());
}
}
