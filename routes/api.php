<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CarrierController;
use App\Http\Controllers\Api\V1\ExceptionController;
use App\Http\Controllers\Api\V1\IntegrationController;
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

        // ETA Evaluation & Recalculation
        Route::get('shipments/{shipment}/eta', [ShipmentController::class, 'evaluateEta'])->name('api.shipments.eta.show');
        Route::patch('shipments/{shipment}/eta', [ShipmentController::class, 'updateEta'])->name('api.shipments.eta.update');

        // Tracking Timeline & Checkpoints
        Route::get('shipments/{shipment}/tracking', [TrackingController::class, 'index'])->name('api.shipments.tracking.index');
        Route::post('shipments/{shipment}/tracking', [TrackingController::class, 'store'])->name('api.shipments.tracking.store');

        // Exception Management
        Route::get('shipments/{shipment}/exceptions', [ExceptionController::class, 'index'])->name('api.shipments.exceptions.index');
        Route::post('shipments/{shipment}/exceptions', [ExceptionController::class, 'store'])->name('api.shipments.exceptions.store');
        Route::match(['put', 'patch'], 'exceptions/{exception}', [ExceptionController::class, 'update'])->name('api.exceptions.update');

        // Global KPIs
        Route::get('analytics/kpis', [ShipmentController::class, 'kpis'])->name('api.analytics.kpis');

        // External Integrations (Weather & Geocoding)
        Route::get('shipments/{shipment}/weather', [IntegrationController::class, 'shipmentWeather'])->name('api.shipments.weather');
        Route::get('integrations/geocode', [IntegrationController::class, 'geocode'])->name('api.integrations.geocode');
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
