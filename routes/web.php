<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ShipmentWebController;
use Illuminate\Support\Facades\Route;

// Public / Landing
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Consignment Detail & Public/Auth Tracking Timeline View
Route::get('/shipments/{trackingNumber}', [ShipmentWebController::class, 'show'])->name('shipments.show');

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

    // Add Tracking Checkpoint (Admin / Operator)
    Route::post('/shipments/{trackingNumber}/events', [ShipmentWebController::class, 'storeEvent'])->name('shipments.events.store');
});
