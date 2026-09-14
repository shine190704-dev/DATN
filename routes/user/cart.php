<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\CartController;


/*
|--------------------------------------------------------------------------
| GIỎ HÀNG
|--------------------------------------------------------------------------
*/

Route::get('/gio-hang', [CartController::class, 'index'])
    ->name('cart.index');

Route::post('/gio-hang/them', [CartController::class, 'add'])
    ->name('cart.add');

Route::post('/gio-hang/cap-nhat', [CartController::class, 'update'])
    ->name('cart.update');

Route::post('/gio-hang/xoa', [CartController::class, 'remove'])
    ->name('cart.remove');