<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\CategoryController;
use App\Http\Controllers\user\ProductController;

Route::get('/tim-kiem', [CategoryController::class, 'search'])
    ->name('product.search');

Route::get('/san-pham/chi-tiet/{id}', [ProductController::class, 'detail'])
    ->name('product.detail');