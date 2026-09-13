<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\WishlistController;


// =========================================================
// SẢN PHẨM YÊU THÍCH
// =========================================================

// Trang danh sách sản phẩm yêu thích
Route::get('/san-pham-yeu-thich', [WishlistController::class, 'index'])
    ->name('wishlist.index');


// Thêm / bỏ sản phẩm yêu thích
Route::post('/san-pham-yeu-thich/toggle', [WishlistController::class, 'toggle'])
    ->name('wishlist.toggle');