<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\Account\AuthController;

Route::get('/dang-nhap', [AuthController::class, 'showLogin'])
    ->name('login');

Route::get('/dang-ky', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/dang-ky', [AuthController::class, 'register'])
    ->name('register.submit');

Route::post('/dang-nhap', [AuthController::class, 'login'])
    ->middleware('throttle:5,1') // tối đa 5 lần / 1 phút, chống brute-force mật khẩu
    ->name('login.submit');

Route::post('/dang-xuat', [AuthController::class, 'logout'])
    ->name('logout');


// =========================================================
// QUÊN MẬT KHẨU
// =========================================================

Route::get('/quen-mat-khau', [AuthController::class, 'showForgotPassword'])
    ->name('password.request');

Route::post('/quen-mat-khau', [AuthController::class, 'sendResetLink'])
    ->middleware('throttle:3,1') // tối đa 3 lần / 1 phút, chống spam gửi mail
    ->name('password.email');

// Trang hiển thị form nhập mật khẩu mới (link trong email trỏ vào đây)
Route::get('/dat-lai-mat-khau/{token}', [AuthController::class, 'showResetPassword'])
    ->name('password.reset');

// Xử lý submit mật khẩu mới
Route::post('/dat-lai-mat-khau', [AuthController::class, 'resetPassword'])
    ->name('password.update');