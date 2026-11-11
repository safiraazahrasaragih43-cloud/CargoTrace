<?php

use App\Http\Controllers\Api\V1\IntegrationController;
use App\Http\Controllers\Web\AuditLogWebController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CarrierWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LocationWebController;
use App\Http\Controllers\Web\NotificationWebController;
use App\Http\Controllers\Web\ShipmentWebController;
use Illuminate\Support\Facades\Route;

// Public / Landing
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public Live Quick Track Page
Route::get('/track', [ShipmentWebController::class, 'trackPage'])->name('tracking.page');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Application Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Shipments Manifest Management
    Route::get('/shipments', [ShipmentWebController::class, 'index'])->name('shipments.index');
    Route::post('/shipments', [ShipmentWebController::class, 'store'])->name('shipments.store');
    Route::post('/shipments/{trackingNumber}/events', [ShipmentWebController::class, 'storeEvent'])->name('shipments.events.store');
    Route::post('/shipments/{trackingNumber}/exceptions', [ShipmentWebController::class, 'storeException'])->name('shipments.exceptions.store');
    Route::patch('/exceptions/{exception}', [ShipmentWebController::class, 'updateException'])->name('exceptions.update');
    Route::patch('/shipments/{trackingNumber}/eta', [ShipmentWebController::class, 'updateEta'])->name('shipments.eta.update');

    // Carriers Management (Admin / Operator)
    Route::get('/carriers', [CarrierWebController::class, 'index'])->name('carriers.index');
    Route::post('/carriers', [CarrierWebController::class, 'store'])->name('carriers.store');
    Route::delete('/carriers/{carrier}', [CarrierWebController::class, 'destroy'])->name('carriers.destroy');

    // Ports & Locations Management
    Route::get('/locations', [LocationWebController::class, 'index'])->name('locations.index');
    Route::post('/locations', [LocationWebController::class, 'store'])->name('locations.store');
    Route::delete('/locations/{location}', [LocationWebController::class, 'destroy'])->name('locations.destroy');

    // Web Geocode Helper (Nominatim API)
    Route::get('/integrations/geocode', [IntegrationController::class, 'geocode'])->name('web.integrations.geocode');

    // Notifications & Audit Logs
    Route::get('/notifications', [NotificationWebController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationWebController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationWebController::class, 'markAllRead'])->name('notifications.read_all');
    Route::get('/audit-logs', [AuditLogWebController::class, 'index'])->name('audit_logs.index');
});

// Consignment Detail & Public/Auth Tracking Timeline View + Weather Integration
Route::get('/shipments/{trackingNumber}', [ShipmentWebController::class, 'show'])->name('shipments.show');
Route::get('/integrations/shipments/{trackingNumber}/weather', [ShipmentWebController::class, 'weather'])->name('web.shipments.weather');
