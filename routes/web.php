<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Shop\ProductController as ShopProductController;


Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('products', ProductController::class);
         Route::resource('categories', CategoryController::class);
    });


Route::get('/shop', [ShopProductController::class, 'index'])
    ->name('shop.products');


    
require __DIR__.'/auth.php';
