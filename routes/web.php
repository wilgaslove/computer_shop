<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\HeroSliderController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;


/*
|--------------------------------------------------------------------------
| Boutique
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

Route::middleware(['auth', 'role:admin|manager'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Produits
        |--------------------------------------------------------------------------
        */

        Route::resource('products', AdminProductController::class);


        /*
        |--------------------------------------------------------------------------
        | Hero Sliders
        |--------------------------------------------------------------------------
        */

        Route::post(
            'hero-sliders/{heroSlider}/toggle',
            [HeroSliderController::class, 'toggle']
        )->name('hero-sliders.toggle');

        Route::resource('hero-sliders', HeroSliderController::class);


        /*
        |--------------------------------------------------------------------------
        | Utilisateurs
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:admin')->group(function () {

            Route::get('/users', [AdminUserController::class, 'index'])
                ->name('users.index');

            Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])
                ->name('users.role.update');
        });
    });
/*
|--------------------------------------------------------------------------
| Profil
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


/*
|--------------------------------------------------------------------------
| Authentification
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
