<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOverviewController;

/*
|--------------------------------------------------------------------------
| ADMIN AUTH
|--------------------------------------------------------------------------
*/


// Đăng nhập
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])
    ->name('admin.login');

Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->name('admin.login.submit');


// Quên mật khẩu
Route::get('/admin/forgot-password', [AdminAuthController::class, 'showForgotPassword'])
    ->name('admin.password.request');

Route::post('/admin/forgot-password', [AdminAuthController::class, 'sendResetLink'])
    ->name('admin.password.email');


// Đặt lại mật khẩu
Route::get('/admin/reset-password/{token}', [AdminAuthController::class, 'showResetPassword'])
    ->name('admin.password.reset');

Route::post('/admin/reset-password', [AdminAuthController::class, 'resetPassword'])
    ->name('admin.password.update');


// Đăng xuất
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])
    ->name('admin.logout');



/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->name('admin.dashboard');


Route::get('/admin/overview', [AdminOverviewController::class, 'index'])
    ->name('admin.overview');