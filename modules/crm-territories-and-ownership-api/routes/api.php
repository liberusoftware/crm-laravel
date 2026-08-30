<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\TerritoriesAndOwnershipApi\Http\Controllers\TerritoryController;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('api/v1/crm/territories-and-ownership')->group(function () {
    Route::get('/rules', [TerritoryController::class, 'index']);
    Route::post('/rules', [TerritoryController::class, 'store']);
    Route::post('/ownership', [TerritoryController::class, 'assign']);
    Route::post('/coverage', [TerritoryController::class, 'coverage']);
    Route::get('/history', [TerritoryController::class, 'history']);
});
