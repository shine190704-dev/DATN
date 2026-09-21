<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\Account\ReviewController;


// Trang danh sách đánh giá
Route::get(
    '/danh-gia-cua-toi',
    [ReviewController::class, 'index']
)->name('reviews.index');


// Gửi đánh giá sản phẩm
Route::post(
    '/danh-gia',
    [ReviewController::class, 'store']
)->name('reviews.store');