<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
});


Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/forgot_password', function () {
    return view('forgot_password');
});

Route::get('/reset_password', function () {
    return view('reset_password');
});


// تسجيل الدخول
Route::post('/login', [UserController::class, 'login'])->name('login.submit');

// تسجيل الخروج
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// نسيت كلمة المرور - إرسال رابط
Route::post('/forgot_password', [UserController::class, 'sendResetLink'])->name('password.email');

// إعادة تعيين كلمة المرور - حفظ الجديدة
Route::post('/reset_password', [UserController::class, 'resetPassword'])->name('password.update');



