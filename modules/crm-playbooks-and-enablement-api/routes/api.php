<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\PlaybooksAndEnablementApi\Http\Controllers\PlaybooksAndEnablementController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/playbooks-and-enablement')->group(function (): void {
    Route::get('/playbooks', [PlaybooksAndEnablementController::class, 'playbooks']);
    Route::post('/playbooks', [PlaybooksAndEnablementController::class, 'playbook']);
    Route::post('/assignments', [PlaybooksAndEnablementController::class, 'assignment']);
    Route::post('/assignments/{assignment}/complete', [PlaybooksAndEnablementController::class, 'complete']);
    Route::post('/recommendations', [PlaybooksAndEnablementController::class, 'recommendation']);
    Route::post('/usage', [PlaybooksAndEnablementController::class, 'usage']);
});
