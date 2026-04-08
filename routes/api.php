<?php

use App\Presentation\Http\Controllers\AuthController;
use App\Presentation\Http\Controllers\NotificationController;
use App\Presentation\Http\Controllers\ToastController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::get('/count', [NotificationController::class, 'count']);
        Route::post('/{notificationId}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notificationId}', [NotificationController::class, 'destroy']);
    });

    Route::prefix('toasts')->group(function () {
        Route::post('/broadcast', [ToastController::class, 'broadcast']);
        Route::post('/broadcast-user', [ToastController::class, 'broadcastToUser']);
        Route::get('/types', [ToastController::class, 'types']);
        Route::post('/', [ToastController::class, 'store']);
    });
});
