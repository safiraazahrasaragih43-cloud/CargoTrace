<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CarrierController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\ShipmentController;
use App\Http\Controllers\Api\V1\TrackingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        // Authenticated auth routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    // Public Tracking Endpoint
    Route::get('/tracking/{trackingNumber}', [ShipmentController::class, 'track']);

    // Protected API Endpoints (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        // Carriers CRUD
        Route::apiResource('carriers', CarrierController::class)->names('api.carriers');

        // Locations CRUD
        Route::apiResource('locations', LocationController::class)->names('api.locations');

        // Shipments CRUD
        Route::apiResource('shipments', ShipmentController::class)->names('api.shipments');

        // Tracking Timeline & Checkpoints
        Route::get('shipments/{shipment}/tracking', [TrackingController::class, 'index'])->name('api.shipments.tracking.index');
        Route::post('shipments/{shipment}/tracking', [TrackingController::class, 'store'])->name('api.shipments.tracking.store');
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
