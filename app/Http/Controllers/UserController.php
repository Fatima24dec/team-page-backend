<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    $email = strtolower(trim($request->email));

    // نبحث عن المستخدم بغض النظر عن حالة الحروف
    $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

    if (!$user) {
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __('messages.email_not_found')])
            ->with('step', 'login');
    }

    // نسجل دخول بالإيميل المخزن بالداتابيس
    if (Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
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
            return back()
                ->withErrors(['email' => __('messages.email_not_found')])
                ->withInput()
                ->with('step', 'forgot');
        }

        $code = rand(1000, 9999);
        Cache::put('reset_code_' . $email, $code, now()->addMinutes(10));

        Mail::raw("Your password reset code is: $code", function ($message) use ($email) {
            $message->to($email)->subject('Password Reset Code');
        });

        return back()
            ->with('status', __('messages.code_sent'))
            ->with('email', $email)
            ->with('step', 'code');
    }

    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required',
        ]);

        $email = strtolower(trim($request->email));
        $cached = Cache::get('reset_code_' . $email);

        if (!$cached || $cached != $request->code) {
            return back()
                ->withErrors(['code' => __('messages.invalid_code')])
                ->withInput()
                ->with('step', 'code');
        }

        session(['reset_email' => $email, 'reset_code' => $request->code, 'codeVerified' => true]);

        return back()->with('codeVerified', true)->with('step', 'newPassword');
    }

    public function resetPasswordWithCode(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'code'                  => 'required',
            'password'              => 'required|min:8|confirmed',
        ]);

        $email = strtolower(trim($request->email));
        $cached = Cache::get('reset_code_' . $email);

        if (!$cached || $cached != $request->code) {
            return back()
                ->withErrors(['code' => __('messages.invalid_code')])
                ->with('step', 'newPassword');
        }

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return back()
                ->withErrors(['email' => __('messages.email_not_found')])
                ->with('step', 'newPassword');
        }

        $user->update(['password' => Hash::make($request->password)]);
        Cache::forget('reset_code_' . $email);
        session()->forget(['reset_email', 'reset_code', 'codeVerified']);

        return redirect('/login')->with('status', __('messages.password_reset_success'));
    }

    public function updateRole(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate(['role' => 'required|in:admin,user']);
        $user->update(['role' => $request->role]);

        return redirect()->route('team.dashboard')
            ->with('success', __('messages.role_updated'));
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
            'password' => Hash::make($password),
            'role'     => 'user',
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
}