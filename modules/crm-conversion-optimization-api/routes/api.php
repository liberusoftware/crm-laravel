<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\ConversionOptimizationApi\Http\Controllers\ConversionOptimizationController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/conversion-optimization')->group(function (): void {
    Route::get('experiments', [ConversionOptimizationController::class, 'index']);
    Route::post('experiments', [ConversionOptimizationController::class, 'store']);
    Route::post('experiments/{experiment}/activate', [ConversionOptimizationController::class, 'activate']);
    Route::post('experiments/{experiment}/conversions', [ConversionOptimizationController::class, 'convert']);
    Route::get('experiments/{experiment}/report', [ConversionOptimizationController::class, 'report']);
});
