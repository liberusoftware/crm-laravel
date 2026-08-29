<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\TelephonyApi\Http\Controllers\TelephonyController;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('api/v1/crm/telephony')->group(function (): void {
    Route::get('/numbers', [TelephonyController::class, 'numbers']);
    Route::post('/numbers', [TelephonyController::class, 'number']);
    Route::get('/queues', [TelephonyController::class, 'queues']);
    Route::post('/queues', [TelephonyController::class, 'queue']);
    Route::get('/settings', [TelephonyController::class, 'settings']);
    Route::put('/settings', [TelephonyController::class, 'configure']);
    Route::get('/calls', [TelephonyController::class, 'calls']);
    Route::post('/calls', [TelephonyController::class, 'logCall']);
    Route::patch('/calls/{call}', [TelephonyController::class, 'updateCall']);
});
