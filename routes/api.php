<?php

// use App\Http\Controllers\Auth\RegisteredUserController;
// use App\Http\Controllers\Auth\AuthenticatedSessionController;
// use Illuminate\Support\Facades\Route;

// Route::get('/test', function () {
//     return response()->json([
//         'success' => true,
//         'message' => 'API Laravel fonctionne correctement.',
//     ]);
// });

// Route::post('/register', [
//     RegisteredUserController::class,
//     'apiRegister'
// ]);

// Route::post('/login', [
//     AuthenticatedSessionController::class,
//     'apiLogin'
// ]);

// Notification serveur KkiaPay (URL à saisir dans le dashboard : https://votre-domaine/api/webhooks/kkiapay)
\Illuminate\Support\Facades\Route::post('/webhooks/kkiapay', \App\Http\Controllers\Webhooks\KkiapayWebhookController::class)
    ->name('webhooks.kkiapay');
