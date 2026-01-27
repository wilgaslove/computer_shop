<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;

/*
|--------------------------------------------------------------------------
| Public (users)
|--------------------------------------------------------------------------
*/
// Route::get('/', [ShopProductController::class, 'index'])
//     ->name('shop.products.index');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('products', AdminProductController::class);
        // Route::resource('categories', CategoryController::class);
    });


require __DIR__.'/auth.php';
