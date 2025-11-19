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
use App\Http\Controllers\Client\HomeController; // Giữ lại HomeController cho các route khác
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\ProfileController; // Đã có sẵn
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\OrderController; // Sửa import để trỏ đúng đến Client\OrderController
// use App\Http\Controllers\Admin\DashboardController; // Giả sử bạn có DashboardController

// Import ProductController cho phần client
use App\Http\Controllers\ProductController as ClientProductController;

// Nhóm các route admin lại
Route::prefix('admin')->name('admin.')->group(function () { // <-- ADMIN ROUTES
    // Các route đăng nhập/đăng xuất admin (không cần middleware 'auth' ở đây, hoặc dùng 'guest:admin')
    Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

    // Nhóm tất cả các route cần xác thực admin
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Các resource controller cho admin
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('attribute_values', AttributeValueController::class);
        Route::resource('comments', CommentController::class);
        Route::resource('accounts', \App\Http\Controllers\Admin\AccountController::class);

        // Các route hành động khác
        Route::post('/comments/bulk', [CommentController::class, 'bulk'])->name('comments.bulk');
    });
});


// =================================================================
// CLIENT ROUTES
// =================================================================
Route::name('client.')->group(function () {
    // Authentication
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Cart
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');

    // Routes require authentication
    Route::middleware('auth:client')->group(function () {
        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/districts', [ProfileController::class, 'getDistricts'])->name('profile.districts');
        Route::get('/profile/wards', [ProfileController::class, 'getWards'])->name('profile.wards');

        // Checkout
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout.process');
        Route::get('/checkout/districts', [CheckoutController::class, 'getDistricts'])->name('checkout.districts');
        Route::get('/checkout/wards', [CheckoutController::class, 'getWards'])->name('checkout.wards');

        // Client comment submission
        Route::post('/product/{id}/comments', [\App\Http\Controllers\Client\CommentController::class, 'store'])->name('product.comment.store');

        // Order History (danh sách đơn hàng)
        Route::get('/don-hang', [OrderController::class, 'index'])->name('orders.index');
        // Order Detail (chi tiết đơn hàng)
        Route::get('/don-hang/{id}', [OrderController::class, 'show'])->name('orders.show');
    });
});

// General Client Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{id}', [HomeController::class, 'showCategory'])->name('category.show');
Route::resource('products', ClientProductController::class)->only(['show']); // Chỉ giữ lại route 'show' cho client

// Payment Gateway Callbacks (không nên nằm trong middleware 'auth')
Route::post('/momo_payment', [CheckoutController::class, 'momopayment'])->name('momo.payment');
Route::post('/momo/ipn', [CheckoutController::class, 'momoIpn'])->name('momo.ipn');
Route::get('/momo/return', [CheckoutController::class, 'momoReturn'])->name('momo.return');

Route::post('/vnpay_payment', [CheckoutController::class, 'vnpay_payment'])->name('vnpay.payment');
Route::get('/vnpay/return', [CheckoutController::class, 'vnpayReturn'])->name('vnpay.return');

// Order Success Page
Route::get('/order/success', function () {
    return view('client.success');
})->name('order.success');
