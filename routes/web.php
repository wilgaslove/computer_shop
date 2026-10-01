<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\HeroSliderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\AccountOrderController;
use App\Http\Controllers\Shop\ContactController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\NewsletterController;
use App\Http\Controllers\Admin\SiteContentController;
use App\Http\Controllers\Admin\NewsletterSubscriberController as AdminNewsletterController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;

/*
|--------------------------------------------------------------------------
| Boutique
|--------------------------------------------------------------------------
*/

// Page d'accueil (contenu modifiable depuis Admin > Accueil du site)
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

// Catalogue

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
        | Commandes
        |--------------------------------------------------------------------------
        */

        Route::get('orders', [AdminOrderController::class, 'index'])
            ->name('orders.index');

        Route::get('orders/{order}', [AdminOrderController::class, 'show'])
            ->name('orders.show');

        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('orders.status');

        Route::patch('orders/{order}/payment', [AdminOrderController::class, 'updatePayment'])
            ->name('orders.payment');


        /*
        |--------------------------------------------------------------------------
        | Messages de contact
        |--------------------------------------------------------------------------
        */

        Route::get('contact-messages', [AdminContactMessageController::class, 'index'])
            ->name('contact-messages.index');

        Route::get('contact-messages/{contactMessage}', [AdminContactMessageController::class, 'show'])
            ->name('contact-messages.show');

        Route::post('contact-messages/{contactMessage}/reply', [AdminContactMessageController::class, 'reply'])
            ->name('contact-messages.reply');

        Route::patch('contact-messages/{contactMessage}/status', [AdminContactMessageController::class, 'updateStatus'])
            ->name('contact-messages.status');

        Route::get('contact-messages/{contactMessage}/attachment', [AdminContactMessageController::class, 'attachment'])
            ->name('contact-messages.attachment');


        /*
        |--------------------------------------------------------------------------
        | Contenu du site (page d'accueil, coordonnées, pied de page)
        |--------------------------------------------------------------------------
        */

        Route::get('site-content', [SiteContentController::class, 'edit'])
            ->name('site-content.edit');

        Route::put('site-content', [SiteContentController::class, 'update'])
            ->name('site-content.update');


        /*
        |--------------------------------------------------------------------------
        | Newsletter
        |--------------------------------------------------------------------------
        */

        Route::get('newsletter', [AdminNewsletterController::class, 'index'])
            ->name('newsletter.index');

        Route::get('newsletter/export', [AdminNewsletterController::class, 'export'])
            ->name('newsletter.export');

        Route::delete('newsletter/{subscriber}', [AdminNewsletterController::class, 'destroy'])
            ->name('newsletter.destroy');


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

Route::get('/products/{product}', [ProductController::class, 'show'])
    ->name('shop.products.show');


/*
|--------------------------------------------------------------------------
| Promotions
|--------------------------------------------------------------------------
*/

Route::get('/promotions', [ProductController::class, 'promotions'])
    ->name('shop.promotions');


/*
|--------------------------------------------------------------------------
| Contact (public)
|--------------------------------------------------------------------------
*/

Route::get('/contact', [ContactController::class, 'show'])
    ->name('contact');

// Limite anti-spam : 5 messages toutes les 10 minutes par visiteur
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('contact.store');


/*
|--------------------------------------------------------------------------
| Newsletter (public)
|--------------------------------------------------------------------------
*/

Route::post('/newsletter', [NewsletterController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('newsletter.subscribe');


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


/*
|--------------------------------------------------------------------------
| Checkout (client connecté)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('checkout')
    ->name('checkout.')
    ->group(function () {

        Route::get('/', [CheckoutController::class, 'index'])
            ->name('index');

        Route::post('/', [CheckoutController::class, 'store'])
            ->name('store');

        Route::get('/confirmation/{order}', [CheckoutController::class, 'success'])
            ->name('success');
    });


/*
|--------------------------------------------------------------------------
| Espace client
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('account')
    ->name('account.')
    ->group(function () {

        Route::get('/orders', [AccountOrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/orders/{order}', [AccountOrderController::class, 'show'])
            ->name('orders.show');

        Route::patch('/orders/{order}/cancel', [AccountOrderController::class, 'cancel'])
            ->name('orders.cancel');
    });


require __DIR__ . '/auth.php';
