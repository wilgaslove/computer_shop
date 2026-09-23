<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\HeroSliderController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;

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
        | Catégories
        |--------------------------------------------------------------------------
        */

        Route::resource('categories', AdminCategoryController::class);


        /*
        |--------------------------------------------------------------------------
        | Produits
        |--------------------------------------------------------------------------
        */

        Route::resource('products', AdminProductController::class);

        Route::delete(
            'products/{product}/images/{image}',
            [AdminProductController::class, 'destroyImage']
        )->name('products.images.destroy');


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


/*|--------------------------------------------------------------------------
| Route Publique
|--------------------------------------------------------------------------
*/

Route::get('/', [ProductController::class, 'index'])
    ->name('shop.products.index');


Route::get('/products/{product}', [ProductController::class, 'show'])
    ->name('shop.products.show');


/*
|--------------------------------------------------------------------------
| Panier
|--------------------------------------------------------------------------
*/

Route::prefix('cart')
    ->name('cart.')
    ->group(function () {

        Route::get('/', [CartController::class, 'index'])
            ->name('index');

        Route::post('/{product}', [CartController::class, 'store'])
            ->name('store');

        Route::patch('/{product}', [CartController::class, 'update'])
            ->name('update');

        Route::delete('/{product}', [CartController::class, 'destroy'])
            ->name('destroy');

        Route::delete('/', [CartController::class, 'clear'])
            ->name('clear');
    });


require __DIR__ . '/auth.php';
