<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials['email'] = strtolower(trim($credentials['email']));

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()
            ->with('step', 'login')
            ->withErrors(['email' => __('messages.invalid_credentials')])
            ->onlyInput('email');
    }

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
            abort(403, __('messages.no_permission_role'));
        }

        $protectedAdminId = 1;

        if ($user->id === $protectedAdminId) {
            return back()->withErrors(['role' => __('messages.protected_admin')]);
        }

        if ($user->id === Auth::id()) {
            return back()->withErrors(['role' => __('messages.no_self_role_change')]);
        }

        $request->validate([
            'role' => 'required|in:admin,user'
        ]);

        $user->update([
            'role' => $request->role
        ]);

        return back()->with('success', __('messages.role_updated'));
    }

    public function invite(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, __('messages.no_permission_invite'));
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $validated['email'] = strtolower(trim($validated['email']));
        $validated['password'] = \Illuminate\Support\Str::random(32);
        $validated['role'] = 'user';
        $validated['phone'] = '';

        User::create($validated);

        return back()->with('success', __('messages.invite_sent'));
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = strtolower(trim($request->email));

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()
                ->with('step', 'email')
                ->withErrors(['email' => __('messages.email_not_found')])
                ->onlyInput('email');
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
                    $message->to($user->email)
                        ->subject(__('messages.reset_code_mail_subject'));
                }
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Reset code mail failed: ' . $e->getMessage());

            return back()
                ->with('step', 'email')
                ->withErrors(['email' => __('messages.mail_send_error')])
                ->onlyInput('email');
        }

        return back()->with([
            'step' => 'code',
            'status' => __('messages.code_sent'),
            'email' => $email,
        ]);
    }

    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:4',
        ]);

        $email = strtolower(trim($request->email));

        $user = User::where('email', $email)->first();

        if (!$user || $user->reset_code != $request->code) {
            return back()
                ->with('step', 'code')
                ->with('email', $email)
                ->withErrors(['code' => __('messages.code_incorrect')]);
        }

        if (now()->greaterThan($user->reset_code_expires_at)) {
            return back()
                ->with('step', 'code')
                ->with('email', $email)
                ->withErrors(['code' => __('messages.code_expired')]);
        }

        return back()->with([
            'step' => 'password',
            'email' => $email,
            'code' => $request->code,
        ]);
    }

    public function resetPasswordWithCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:4',
            'password' => 'required|min:6|confirmed',
        ]);

        $email = strtolower(trim($request->email));

        $user = User::where('email', $email)->first();

        if (
            !$user ||
            $user->reset_code != $request->code ||
            now()->greaterThan($user->reset_code_expires_at)
        ) {
            return back()
                ->with('step', 'password')
                ->with('email', $email)
                ->with('code', $request->code)
                ->withErrors(['password' => __('messages.reset_generic_error')]);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_code' => null,
            'reset_code_expires_at' => null,
        ]);

        return redirect('/login')->with([
            'status' => __('messages.password_updated'),
            'reset_success' => true,
        ]);
    }
}