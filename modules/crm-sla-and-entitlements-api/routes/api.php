<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\SlaAndEntitlementsApi\Http\Controllers\SlaController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/sla-and-entitlements')->group(function (): void {
    Route::get('/contracts', [SlaController::class, 'contracts']);
    Route::post('/contracts', [SlaController::class, 'storeContract']);
    Route::get('/calendars', [SlaController::class, 'calendars']);
    Route::post('/calendars', [SlaController::class, 'storeCalendar']);
    Route::get('/entitlements', [SlaController::class, 'entitlements']);
    Route::post('/entitlements', [SlaController::class, 'storeEntitlement']);
    Route::get('/cases', [SlaController::class, 'cases']);
    Route::post('/cases', [SlaController::class, 'storeCase']);
    Route::post('/cases/{case}/{transition}', [SlaController::class, 'transition'])->whereIn('transition', ['responded', 'pause', 'resume', 'resolve', 'close']);
    Route::post('/cases/{case}/evaluate', [SlaController::class, 'evaluate']);
    Route::post('/exceptions', [SlaController::class, 'exception']);
});
