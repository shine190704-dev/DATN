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
    ->name('login.submit');

Route::post('/dang-xuat', [AuthController::class, 'logout'])
    ->name('logout');

// QUÊN MẬT KHẨU
Route::get('/quen-mat-khau', [AuthController::class, 'showForgotPassword'])
    ->name('password.request');

Route::post('/quen-mat-khau', [AuthController::class, 'sendResetLink'])
    ->name('password.email');