<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        // Authenticated routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'CargoTrace API v1 operational',
            'timestamp' => now()->toISOString(),
        ]);
    });
});
