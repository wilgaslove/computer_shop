<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Admin
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('products', Admin\ProductController::class);
        Route::resource('categories', Admin\CategoryController::class);
        // Route::resource('orders', Admin\OrderController::class);
    });

// Front (clients)
// Route::get('/', [Shop\ProductController::class, 'home'])->name('shop.home');
// Route::get('/products', [Shop\ProductController::class, 'index'])->name('shop.products');
// Route::get('/products/{product}', [Shop\ProductController::class, 'show'])->name('shop.products.show');


require __DIR__.'/auth.php';
