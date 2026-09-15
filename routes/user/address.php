<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\Account\AddressController;


// Sổ địa chỉ
Route::get('/so-dia-chi', [AddressController::class, 'index'])
    ->name('address.index');

Route::get('/so-dia-chi/them', [AddressController::class, 'create'])
    ->name('address.create');

Route::get('/so-dia-chi/{id}/sua', [AddressController::class, 'edit'])
    ->name('address.edit');


// Thêm địa chỉ
Route::post('/so-dia-chi', [AddressController::class, 'store'])
    ->name('address.store');


// Cập nhật địa chỉ
Route::put('/so-dia-chi/{id}', [AddressController::class, 'update'])
    ->name('address.update');


// Đặt địa chỉ mặc định
Route::patch('/so-dia-chi/{id}/mac-dinh', [AddressController::class, 'setDefault'])
    ->name('address.default');


// Xóa địa chỉ
Route::delete('/so-dia-chi/{id}', [AddressController::class, 'destroy'])
    ->name('address.destroy');