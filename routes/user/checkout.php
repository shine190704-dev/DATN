<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\CheckoutController;

Route::get('/thanh-toan', [CheckoutController::class, 'index'])
    ->name('checkout.index');

Route::post('/thanh-toan/dat-hang', [CheckoutController::class, 'placeOrder'])
    ->name('checkout.placeOrder');
    
Route::get('/thanh-toan/thanh-cong/{maDonHang}', [CheckoutController::class, 'success'])
    ->name('checkout.success');

Route::post('/thanh-toan/ap-dung-ma', [CheckoutController::class, 'applyCoupon'])
    ->name('checkout.applyCoupon');