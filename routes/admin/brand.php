<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BrandController;

Route::get('/brand', [BrandController::class, 'index'])
    ->name('admin.brand.index');

Route::post('/brand', [BrandController::class, 'store'])
    ->name('admin.brand.store');

Route::put('/brand/{id}', [BrandController::class, 'update'])
    ->name('admin.brand.update');

Route::delete('/brand/{id}', [BrandController::class, 'destroy'])
    ->name('admin.brand.destroy');
