<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\CategoryController;


// KHÁM PHÁ TẤT CẢ
Route::get('/san-pham', [CategoryController::class, 'all'])
    ->name('product.all');


// SẢN PHẨM MỚI - TRONG 30 NGÀY
Route::get('/san-pham-moi', [CategoryController::class, 'newProducts'])
    ->name('product.new');


// DANH MỤC
Route::get('/danh-muc/{id}', [CategoryController::class, 'show'])
    ->name('category.show');