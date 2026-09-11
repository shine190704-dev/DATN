<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\HomeController;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');