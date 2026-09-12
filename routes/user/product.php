
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\CategoryController;

Route::get('/tim-kiem', [CategoryController::class, 'search'])
	->name('product.search');

