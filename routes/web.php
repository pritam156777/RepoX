<?php

use App\Http\Controllers\SuperAdmin\AdminManagementController;
use App\Http\Controllers\SuperAdmin\CategoryController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;
use App\Models\User;


Route::get('/', [ShopController::class, 'home'])->name('home');

Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('category/{category:uuid}', [ShopController::class, 'category'])->name('category');
    Route::get('{product:uuid}', [ShopController::class, 'show'])->name('show');
});

Route::middleware('auth')->group(function () {

   Route::match(['get','post'], '/cart/add/{uuid}', [CartController::class, 'add'])->name('cart.add');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

    // NEW: Checkout page (GET)
    Route::get('/checkout', [OrderController::class, 'checkoutPage'])->name('cart.checkout');

    // NEW: AJAX create PaymentIntent
    Route::post('/cart/create-payment-intent', [OrderController::class, 'createPaymentIntent'])->name('cart.createPaymentIntent');

    // Checkout submit (POST)
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.store');

    // Checkout success page
    Route::get('/checkout/success', [OrderController::class, 'success'])->name('checkout.success');

    Route::delete('/cart/remove/{product:uuid}', [CartController::class, 'remove'])->name('cart.remove');
});


Route::get('/dev/create-folders', function () {
  if (User::where('email', 'pritam156777@gmail.com')->exists()) {
        return '❌ Super Admin already exists. DELETE THIS ROUTE.';
    }

    User::create([
        'name' => 'Pritam Maurya',
        'email' => 'pritam156777@gmail.com',
        'password' => Hash::make('pintu@987'),
        'role' => 'super_admin',
    ]);

    return '✅ Super Admin created successfully. DELETE THIS ROUTE NOW!';
});

/*
|--------------------------------------------------------------------------
| CART (AUTH REQUIRED)
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
| ROLE BASED DASHBOARD REDIRECT
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
| SUPER ADMIN
|--------------------------------------------------------------------------
*/
/*Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->group(function () {

        Route::get('/dashboard', [SuperAdminDashboardController::class, 'dashboard'])
            ->name('super-admin.dashboard');
    });*/



Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->group(function () {

        Route::get('/dashboard', [SuperAdminDashboardController::class, 'dashboard'])
            ->name('super-admin.dashboard');

        /*
        |--------------------------------------------------------------------------
        | 🔥 ADMIN MANAGEMENT (ADDED)
        |--------------------------------------------------------------------------
        */
        Route::get('/admins', [AdminManagementController::class, 'store'])
            ->name('super-admin.admins.index');


        Route::get('/admins/create', [AdminManagementController::class, 'create'])->name('super-admin.admins.create');
        Route::post('/admins', [AdminManagementController::class, 'store'])->name('super-admin.admins.store');


        Route::get('/users/create', [UserManagementController::class, 'create'])->name('super-admin.users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('super-admin.users.store');


        Route::get('categories/create', [CategoryController::class, 'create'])
            ->name('super-admin.categories.create');

        Route::post('categories/store', [CategoryController::class, 'store'])
            ->name('super-admin.categories.store');

        Route::delete('categories/{category}', [CategoryController::class, 'destroy']) //its working important for super admin
            ->name('super-admin.categories.destroy');

    });

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

/*GET|HEAD  super-admin/dashboard        super-admin.dashboard
GET|HEAD  super-admin/admins           super-admin.admins.index
GET|HEAD  super-admin/admins/create    super-admin.admins.create
POST      super-admin/admins           super-admin.admins.store

*/


Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])
            ->name('admin.dashboard');

        // Admin Products CRUD
        Route::resource('products', ProductController::class);
    });

/*
|--------------------------------------------------------------------------
| USER (SELLER)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->group(function () {

        Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])
            ->name('user.dashboard');
    });







Route::get('/orders/pdf', [OrderController::class, 'generatePdf'])->name('orders.pdf');

/*Route::get('/stripe-test', function () {
    return [
        'key' => config('services.stripe.key'),
        'secret' => substr(config('services.stripe.secret'), 0, 10) . '***'
    ];
});*/
Route::get('/env-check', function () {
    return env('APP_URL', 'NOT LOADED');
});


