<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'apiRegister']);

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Laravel fonctionne correctement.',
    ]);
});