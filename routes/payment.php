<?php

use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\Vendor\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
|
| Here is where you can register payment-related routes for your application.
|
*/

// Vendor Dashboard Payment Routes
Route::middleware(['auth', 'verified', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    // Payment Settings
    Route::prefix('payment')->name('payment.')->group(function () {
        // Main payment dashboard
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        
        // Payment history
        Route::get('/history', [PaymentController::class, 'history'])->name('history');
        
        // Payment Methods
        Route::prefix('methods')->name('methods.')->group(function () {
            Route::get('/create', [PaymentController::class, 'createPaymentMethod'])->name('create');
            Route::post('/', [PaymentController::class, 'storePaymentMethod'])->name('store');
            Route::get('/{id}/edit', [PaymentController::class, 'editPaymentMethod'])->name('edit');
            Route::put('/{id}', [PaymentController::class, 'updatePaymentMethod'])->name('update');
            Route::delete('/{id}', [PaymentController::class, 'destroyPaymentMethod'])->name('destroy');
        });
        
        // Payout Methods
        Route::prefix('payout-methods')->name('payout-methods.')->group(function () {
            Route::get('/create', [PaymentController::class, 'createPayoutMethod'])->name('create');
            Route::post('/', [PaymentController::class, 'storePayoutMethod'])->name('store');
            Route::get('/{id}/edit', [PaymentController::class, 'editPayoutMethod'])->name('edit');
            Route::put('/{id}', [PaymentController::class, 'updatePayoutMethod'])->name('update');
            Route::delete('/{id}', [PaymentController::class, 'destroyPayoutMethod'])->name('destroy');
        });
        
        // Payout Preferences
        Route::prefix('preferences')->name('preferences.')->group(function () {
            Route::put('/', [PaymentController::class, 'updatePayoutPreferences'])->name('update');
        });
    });
});

// API Routes for Payment Methods
Route::middleware(['auth:sanctum'])->prefix('api')->group(function () {
    Route::apiResource('payment-methods', PaymentMethodController::class);
    
    // Additional payment-related API routes can be added here
});
