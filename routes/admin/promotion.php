
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PromotionController;

// QUẢN LÝ MÃ KHUYẾN MÃI
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/promotion', [PromotionController::class, 'index'])
            ->name('promotion.index');

        Route::post('/promotion', [PromotionController::class, 'store'])
            ->name('promotion.store');

        Route::put('/promotion/{id}', [PromotionController::class, 'update'])
            ->name('promotion.update');

        Route::delete('/promotion/{id}', [PromotionController::class, 'destroy'])
            ->name('promotion.destroy');

    });
