<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\user\ProfileController;
use App\Http\Controllers\user\Account\PasswordController;


// =========================
// THÔNG TIN CÁ NHÂN
// =========================

Route::get('/thong-tin-ca-nhan', [ProfileController::class, 'index'])
    ->name('profile');


Route::get('/thong-tin-ca-nhan/chinh-sua', [ProfileController::class, 'edit'])
    ->name('profile.edit');


Route::put('/thong-tin-ca-nhan', [ProfileController::class, 'update'])
    ->name('profile.update');


// =========================
// ĐỔI MẬT KHẨU
// =========================

Route::get('/doi-mat-khau', [PasswordController::class, 'index'])
    ->name('password.edit');


Route::post('/doi-mat-khau', [PasswordController::class, 'update'])
    ->name('account.password.update');