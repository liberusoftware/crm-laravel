<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\ServiceAnalyticsApi\Http\Controllers\AnalyticsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/service-analytics')->group(function (): void {
    Route::get('/snapshots', [AnalyticsController::class, 'index']);
    Route::get('/metrics/{metric}', [AnalyticsController::class, 'metric']);
    Route::get('/summary', [AnalyticsController::class, 'summary']);
    Route::post('/snapshots', [AnalyticsController::class, 'store']);
    Route::post('/snapshots/batch', [AnalyticsController::class, 'batch']);
});
