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
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\OrderController;

 
Route::prefix('admin')->group(function () {
    Route::resource('categories', CategoryController::class);
   Route::resource('products', ProductController::class);
  Route::resource('attribute_values', AttributeValueController::class);
  Route::resource('comments', CommentController::class);
 Route::resource('accounts', AccountController::class)->except(['create', 'store']);
 Route::resource('orders', AdminOrderController::class);


});
// Admin bulk actions for comments
Route::post('admin/comments/bulk', [CommentController::class, 'bulk'])->name('comments.bulk');
// Client comment submission (requires client auth)
Route::post('/product/{id}/comments', [CommentController::class, 'store'])
    ->name('product.comment.store')
    ->middleware('auth:client');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{id}', [HomeController::class, 'showCategory'])->name('category.show');
Route::get('/product/{id}', [HomeController::class, 'showProduct'])->name('product.show');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::get('/login', [AuthController::class, 'showLogin'])->name('client.login');
Route::post('/login', [AuthController::class, 'login'])->name('client.login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('client.register');
Route::post('/register', [AuthController::class, 'register'])->name('client.register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('client.logout');
Route::middleware('auth:client')->group(function(){
    Route::get('/profile',[ProfileController::class,'edit'])->name('profile.edit');
    Route::post('/profile',[ProfileController::class,'update'])->name('profile.update');
    Route::get('/profile/districts', [ProfileController::class,'getDistricts'])->name('profile.districts');
Route::get('/profile/wards', [ProfileController::class,'getWards'])->name('profile.wards');
 Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout.process');

// Route for MoMo payment

Route::post('/momo_payment', [CheckoutController::class, 'momopayment'])->name('momo.payment');
Route::post('/momo/ipn', [CheckoutController::class, 'momoIpn'])->name('momo.ipn');
Route::get('/momo/return', [CheckoutController::class, 'momoReturn'])->name('momo.return');

// COD
Route::get('/order/success', function () {
    return view('client.success');
})->name('order.success');
// VNPay
Route::post('/vnpay_payment', [CheckoutController::class, 'vnpay_payment'])->name('vnpay.payment');
Route::get('/vnpay/return', [CheckoutController::class, 'vnpayReturn'])->name('vnpay.return');
// Lịch sử đơn hàng (chỉ client đã đăng nhập)
Route::middleware('auth:client')->group(function () {
    Route::prefix('order-history')->group(function () {
        Route::get('/', [OrderController::class, 'history'])->name('order.history');
        Route::get('/{order_code}', [OrderController::class, 'detail'])->name('order.history.detail');
         Route::post('/order-history/{order_code}/cancel', [OrderController::class, 'cancel'])
        ->name('order.cancel');
    });
});


});


