<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\ProductController;

Route::get('/san-pham', [ProductController::class, 'index'])
    ->name('product.index');