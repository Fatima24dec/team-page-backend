<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeamMembersController;

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('login'))->name('login');
    Route::get('/forgot_password', fn () => view('login'));
    Route::post('/login', [UserController::class, 'login'])->name('login.submit');
    Route::post('/forgot_password', [UserController::class, 'sendResetLink'])->name('password.email');
    Route::post('/reset_password', [UserController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [TeamMembersController::class, 'dashboard'])->name('team.dashboard');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::get('/team/{teamMember}/edit', [TeamMembersController::class, 'edit'])->name('team.edit');
    Route::put('/team/{teamMember}', [TeamMembersController::class, 'update'])->name('team.update');
    Route::delete('/team/{teamMember}', [TeamMembersController::class, 'destroy'])->name('team.destroy');

    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [TeamMembersController::class, 'dashboard'])->name('team.dashboard');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::get('/team/{teamMember}/edit', [TeamMembersController::class, 'edit'])->name('team.edit');
    Route::put('/team/{teamMember}', [TeamMembersController::class, 'update'])->name('team.update');
    Route::delete('/team/{teamMember}', [TeamMembersController::class, 'destroy'])->name('team.destroy');

    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
    Route::post('/users/invite', [UserController::class, 'invite'])->name('users.invite');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    });

    Route::get('/forgot_password', function () {
        return view('login');
    });

    Route::post('/login', [UserController::class, 'login'])->name('login.submit');

    Route::post('/send_reset_code', [UserController::class, 'sendResetCode'])->name('password.send_code');
    Route::post('/verify_reset_code', [UserController::class, 'verifyResetCode'])->name('password.verify_code');
    Route::post('/reset_password', [UserController::class, 'resetPasswordWithCode'])->name('password.update');
});
