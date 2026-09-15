<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\ProfileController;

Route::get('/thong-tin-ca-nhan', [ProfileController::class, 'index'])
    ->name('profile');

Route::get('/thong-tin-ca-nhan/chinh-sua', [ProfileController::class, 'edit'])
    ->name('profile.edit');

Route::put('/thong-tin-ca-nhan', [ProfileController::class, 'update'])
    ->name('profile.update');