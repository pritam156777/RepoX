<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Product;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;

use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

use App\Http\Controllers\User\DashboardController as UserDashboardController;

use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\CategoryController;
use App\Http\Controllers\SuperAdmin\AdminManagementController;
use App\Http\Controllers\SuperAdmin\UserManagementController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [ShopController::class, 'home'])->name('home');

Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('category/{category:uuid}', [ShopController::class, 'category'])->name('category');
    Route::get('{product:uuid}', [ShopController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

/*
|--------------------------------------------------------------------------
| CART & CHECKOUT (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::match(['get', 'post'], '/cart/add/{uuid}', [CartController::class, 'add'])
        ->name('cart.add');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

    Route::delete('/cart/remove/{product:uuid}', [CartController::class, 'remove'])
        ->name('cart.remove');

    Route::get('/checkout', [OrderController::class, 'checkoutPage'])
        ->name('cart.checkout');

    Route::post('/checkout', [OrderController::class, 'checkout'])
        ->name('checkout.store');

    Route::post('/cart/create-payment-intent', [OrderController::class, 'createPaymentIntent'])
        ->name('cart.createPaymentIntent');

    Route::get('/checkout/success', [OrderController::class, 'success'])
        ->name('checkout.success');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD REDIRECT (ROLE BASED)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        'super_admin' => redirect()->route('super-admin.dashboard'),
        'admin'       => redirect()->route('admin.dashboard'),
        default       => redirect()->route('user.dashboard'),
    };
})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| SUPER ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->group(function () {

        Route::get('/dashboard', [SuperAdminDashboardController::class, 'dashboard'])
            ->name('super-admin.dashboard');

        Route::put(
            'categories/{category:uuid}/photo',
            [CategoryController::class, 'updatePhoto']
        )->name('categories.photo.update');

        // Admin management
        Route::get('/admins', [AdminManagementController::class, 'index'])
            ->name('super-admin.admins.index');

        Route::get('/admins/create', [AdminManagementController::class, 'create'])
            ->name('super-admin.admins.create');

        Route::post('/admins', [AdminManagementController::class, 'store'])
            ->name('super-admin.admins.store');

        // User management
        Route::get('/users/create', [UserManagementController::class, 'create'])
            ->name('super-admin.users.create');

        Route::post('/users', [UserManagementController::class, 'store'])
            ->name('super-admin.users.store');

        // Categories
        Route::get('categories/create', [CategoryController::class, 'create'])
            ->name('super-admin.categories.create');

        Route::post('categories/store', [CategoryController::class, 'store'])
            ->name('super-admin.categories.store');

        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])
            ->name('super-admin.categories.destroy');
    });

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::resource('products', ProductController::class);

                //here added new items
        Route::post('/products/update-stock', [ProductController::class, 'updateStock'])
            ->name('products.updateStock');

        Route::delete('/products/{uuid}', [ProductController::class, 'destroyStock'])
            ->name('products.destroyStock');
    });

/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->group(function () {

        Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])
            ->name('user.dashboard');
    });

/*
|--------------------------------------------------------------------------
| OTHER
|--------------------------------------------------------------------------
*/

Route::get('/orders/pdf', [OrderController::class, 'generatePdf'])
    ->name('orders.pdf');

Route::get('/env-check', function () {
    return env('APP_URL', 'NOT LOADED');
});
