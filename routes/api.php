<?php

use App\Http\Controllers\API\JuegoController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Juego management routes
    Route::apiResource('juegos', JuegoController::class);
    Route::post('juegos/{id}/iniciar', [JuegoController::class, 'iniciar']);
    Route::post('juegos/{id}/finalizar', [JuegoController::class, 'finalizar']);
    Route::get('juegos/calendario', [JuegoController::class, 'calendario']);
    Route::get('juegos/proximos', [JuegoController::class, 'proximos']);
});
