<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\RevenueLifecycleApi\Http\Controllers\RevenueLifecycleController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/revenue-lifecycle')->group(function (): void {
    Route::get('/assets', [RevenueLifecycleController::class, 'assets']);
    Route::post('/assets', [RevenueLifecycleController::class, 'asset']);
    Route::post('/assets/{asset}/usage', [RevenueLifecycleController::class, 'usage']);
    Route::post('/orders', [RevenueLifecycleController::class, 'order']);
    Route::get('/fallout', [RevenueLifecycleController::class, 'fallout']);
    Route::post('/fallout/{fallout}/resolve', [RevenueLifecycleController::class, 'resolveFallout']);
});
