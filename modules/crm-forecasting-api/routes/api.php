<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\ForecastingApi\Http\Controllers\ForecastingController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/forecasting')->group(function (): void {
    Route::get('categories', [ForecastingController::class, 'categories']);
    Route::get('forecasts/{period}', [ForecastingController::class, 'index']);
    Route::post('categories', [ForecastingController::class, 'storeCategory']);
    Route::post('forecasts', [ForecastingController::class, 'store']);
    Route::post('forecasts/{forecast}/submit', [ForecastingController::class, 'submit']);
});
