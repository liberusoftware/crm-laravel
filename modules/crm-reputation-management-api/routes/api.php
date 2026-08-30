<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\ReputationManagementApi\Http\Controllers\ReputationManagementController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/reputation-management')->group(function (): void {
    Route::get('/connections', [ReputationManagementController::class, 'connections']);
    Route::post('/connections', [ReputationManagementController::class, 'connection']);
    Route::get('/requests', [ReputationManagementController::class, 'requests']);
    Route::post('/requests', [ReputationManagementController::class, 'request']);
    Route::get('/reviews', [ReputationManagementController::class, 'reviews']);
    Route::post('/reviews', [ReputationManagementController::class, 'review']);
    Route::post('/reviews/{review}/respond', [ReputationManagementController::class, 'respond']);
    Route::post('/templates', [ReputationManagementController::class, 'template']);
});
