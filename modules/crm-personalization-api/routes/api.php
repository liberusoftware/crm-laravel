<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\PersonalizationApi\Http\Controllers\PersonalizationController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/personalization')->group(function (): void {
    Route::get('/rules', [PersonalizationController::class, 'rules']);
    Route::post('/rules', [PersonalizationController::class, 'rule']);
    Route::post('/decisions', [PersonalizationController::class, 'decision']);
    Route::post('/outcomes', [PersonalizationController::class, 'outcome']);
});
