<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\AnalyticsApi\Http\Controllers\AnalyticsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/analytics')->group(function (): void {
    Route::get('assets', [AnalyticsController::class, 'index']);
    Route::post('assets', [AnalyticsController::class, 'store']);
    Route::post('assets/{asset}/execute', [AnalyticsController::class, 'execute']);
});
