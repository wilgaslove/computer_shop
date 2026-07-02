<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;

/*
|--------------------------------------------------------------------------
| Boutique (publique)
|--------------------------------------------------------------------------
*/

Route::get('/', [ShopProductController::class, 'index'])
    ->name('home');

Route::get('/shop', [ShopProductController::class, 'index'])
    ->name('shop.products');


/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('products', AdminProductController::class);

        // Plus tard
        // Route::resource('categories', CategoryController::class);
        // Route::resource('orders', OrderController::class);
        // Route::resource('users', UserController::class);
        Route::resource('hero-sliders', \App\Http\Controllers\Admin\HeroSliderController::class);
    });



/*
|--------------------------------------------------------------------------
| Profil utilisateur
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__ . '/auth.php';
