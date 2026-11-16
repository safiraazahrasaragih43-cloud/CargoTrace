<?php

use App\Http\Controllers\Api\V1\AuditLogController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CarrierController;
use App\Http\Controllers\Api\V1\ExceptionController;
use App\Http\Controllers\Api\V1\IntegrationController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\NotificationController;
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

        // In-App Notifications & Admin Audit Logs
        Route::get('notifications', [NotificationController::class, 'index'])->name('api.notifications.index');
        Route::patch('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('api.notifications.read');
        Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('api.notifications.read_all');
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('api.audit_logs.index');
    });

    // Health check endpoint
    Route::get('/health', function (\Illuminate\Http\Request $request) {
        $now = now()->toISOString();

        $dbConnected = true;
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbConnected = false;
        }

        $jsonPayload = [
            'success' => true,
            'message' => 'CargoTrace API v1 operational',
            'timestamp' => $now,
        ];

        // If accessed directly from browser without JSON preference, render full visual health dashboard
        if ($request->acceptsHtml() && !$request->wantsJson() && $request->query('format') !== 'json') {
            return response()->view('api.health', [
                'jsonPayload' => $jsonPayload,
                'jsonString' => json_encode($jsonPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                'dbConnected' => $dbConnected,
                'timestamp' => $now,
                'phpVersion' => PHP_VERSION,
                'laravelVersion' => app()->version(),
            ], $dbConnected ? 200 : 503);
        }

        return response()->json($jsonPayload, $dbConnected ? 200 : 503);
    });
});

// =========================================================================
// Slide 12 Root Aliases (Allows direct calls like /api/login and /api/shipments)
// =========================================================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/tracking/{trackingNumber}', [ShipmentController::class, 'track']);
Route::get('/health', function (\Illuminate\Http\Request $request) {
    if ($request->acceptsHtml() && !$request->wantsJson()) {
        return redirect('/api/v1/health');
    }
    return response()->json([
        'success' => true,
        'message' => 'CargoTrace API operational',
        'timestamp' => now()->toISOString(),
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me'])->name('api.root.user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.root.logout');
    Route::apiResource('shipments', ShipmentController::class)->names('api.root.shipments');
    Route::get('shipments/{shipment}/tracking', [TrackingController::class, 'index'])->name('api.root.tracking.index');
    Route::post('shipments/{shipment}/tracking', [TrackingController::class, 'store'])->name('api.root.tracking.store');
    Route::get('shipments/{shipment}/exceptions', [ExceptionController::class, 'index'])->name('api.root.exceptions.index');
    Route::post('shipments/{shipment}/exceptions', [ExceptionController::class, 'store'])->name('api.root.exceptions.store');
    Route::match(['put', 'patch'], 'exceptions/{exception}', [ExceptionController::class, 'update'])->name('api.root.exceptions.update');
    Route::apiResource('carriers', CarrierController::class)->names('api.root.carriers');
    Route::apiResource('locations', LocationController::class)->names('api.root.locations');
});
