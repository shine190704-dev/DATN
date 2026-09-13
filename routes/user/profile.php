<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\ProfileController;

Route::get('/thong-tin-ca-nhan', [ProfileController::class, 'index'])
    ->name('profile');

Route::post('/thong-tin-ca-nhan', [ProfileController::class, 'update'])
    ->name('profile.update');