<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\GoalsAndPerformanceApi\Http\Controllers\PerformanceController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/goals-and-performance')->group(function (): void {
    Route::get('/', [PerformanceController::class, 'index']);
    Route::post('/', [PerformanceController::class, 'store']);
    Route::post('{goal}/events', [PerformanceController::class, 'event']);
    Route::post('reviews', [PerformanceController::class, 'review']);
});
