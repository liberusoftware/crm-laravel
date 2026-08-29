<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\ResourcePlanningApi\Http\Controllers\ResourcePlanningController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/resource-planning')->group(function (): void {
    Route::get('/skills', [ResourcePlanningController::class, 'skills']);
    Route::post('/skills', [ResourcePlanningController::class, 'skill']);
    Route::get('/capacity', [ResourcePlanningController::class, 'capacity']);
    Route::post('/capacity', [ResourcePlanningController::class, 'setCapacity']);
    Route::get('/bookings', [ResourcePlanningController::class, 'bookings']);
    Route::post('/bookings', [ResourcePlanningController::class, 'booking']);
    Route::post('/rates', [ResourcePlanningController::class, 'rate']);
    Route::post('/forecasts', [ResourcePlanningController::class, 'forecast']);
});
