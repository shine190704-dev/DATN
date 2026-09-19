<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\user\Account\OrderController;
use App\Http\Controllers\user\Account\OrderTrackingController;


// =========================
// ĐƠN HÀNG CỦA TÔI
// =========================

Route::get('/don-hang-cua-toi', [OrderController::class, 'index'])
    ->name('order.index');


// HỦY ĐƠN
Route::post('/don-hang/{id}/huy', [OrderController::class, 'cancel'])
    ->name('order.cancel');


// ĐÃ NHẬN HÀNG
Route::post('/don-hang/{id}/da-nhan', [OrderController::class, 'receive'])
    ->name('order.receive');


// =========================
// THEO DÕI ĐƠN HÀNG
// =========================

Route::get('/theo-doi-don-hang', [OrderTrackingController::class, 'index'])
    ->name('tracking.index');