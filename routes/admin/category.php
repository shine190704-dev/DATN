<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;

Route::get('/category', [CategoryController::class, 'index'])
    ->name('admin.category.index');

Route::post('/category', [CategoryController::class, 'store'])
    ->name('admin.category.store');

Route::put('/category/{id}', [CategoryController::class, 'update'])
    ->name('admin.category.update');

Route::delete('/category/{id}', [CategoryController::class, 'destroy'])
    ->name('admin.category.destroy');
