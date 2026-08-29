<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\RevenueIntelligenceApi\Http\Controllers\RevenueIntelligenceController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/revenue-intelligence')->group(function (): void {
    Route::get('/insights', [RevenueIntelligenceController::class, 'insights']);
    Route::post('/insights', [RevenueIntelligenceController::class, 'insight']);
    Route::get('/alerts', [RevenueIntelligenceController::class, 'alerts']);
    Route::post('/alerts', [RevenueIntelligenceController::class, 'alert']);
    Route::post('/alerts/{alert}/resolve', [RevenueIntelligenceController::class, 'resolve']);
});
