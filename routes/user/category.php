<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\CategoryController;

Route::get('/danh-muc/{id}', [CategoryController::class, 'show'])
    ->name('category.show');