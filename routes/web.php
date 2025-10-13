<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Product_VariantController;

    Route::prefix('admin')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
    });
Route::get('/products/{id}', [Product_VariantController::class, 'show'])->name('products.show');