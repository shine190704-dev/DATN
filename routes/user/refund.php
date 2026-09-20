<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\Account\RefundController;

Route::get('/yeu-cau-hoan-tien', [RefundController::class, 'index'])
    ->name('refund.index');

Route::post('/yeu-cau-hoan-tien', [RefundController::class, 'store'])
    ->name('refund.store');

Route::get('/yeu-cau-hoan-tien/{id}', [RefundController::class, 'show'])
    ->name('refund.show');