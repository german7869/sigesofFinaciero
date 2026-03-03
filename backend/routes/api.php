<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rutas solo para admin
    Route::middleware(\App\Http\Middleware\EsAdmin::class)->prefix('admin')->group(function () {
        // Aquí se agregarán endpoints administrativos en futuras iteraciones
    });
});
