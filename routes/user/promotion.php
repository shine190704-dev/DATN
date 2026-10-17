<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\PromotionController;
use App\Http\Controllers\User\Account\MyPromotionController;

// Trang khuyến mãi công khai
Route::get('/khuyen-mai', [
    PromotionController::class,
    'index',
])->name('user.promotion.index');

// Khách hàng nhận mã giảm giá
Route::post('/khuyen-mai/{id}/nhan', [
    PromotionController::class,
    'claim',
])->whereNumber('id')->name('user.promotion.claim');

// Trang mã khuyến mãi của tôi
Route::get('/tai-khoan/ma-khuyen-mai', [
    MyPromotionController::class,
    'index',
])->name('user.account.promotions');
