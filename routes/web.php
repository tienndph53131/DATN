<?php



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
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\CartController;
Route::prefix('admin')->group(function () {
    Route::resource('categories', CategoryController::class);
   Route::resource('products', ProductController::class);
  Route::resource('attribute_values', AttributeValueController::class);

});
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{id}', [HomeController::class, 'showCategory'])->name('category.show');
Route::get('/product/{id}', [HomeController::class, 'showProduct'])->name('product.show');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
// Route::get('/shop', function () {
//     return view('layouts.shop');
// });



