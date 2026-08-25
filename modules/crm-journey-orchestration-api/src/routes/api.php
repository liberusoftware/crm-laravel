<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\JourneyOrchestrationApi\Http\Controllers\JourneyController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/journey-orchestration')->group(function (): void {
    Route::get('/', [JourneyController::class, 'index']);
    Route::post('/', [JourneyController::class, 'store']);
    Route::post('{journey}/publish', [JourneyController::class, 'publish']);
    Route::post('{journey}/runs', [JourneyController::class, 'run']);
    Route::post('runs/{run}/stop', [JourneyController::class, 'stop']);
});
