<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\PredictiveModelsApi\Http\Controllers\PredictiveModelsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/predictive-models')->group(function (): void {
    Route::get('/models', [PredictiveModelsController::class, 'models']);
    Route::post('/models', [PredictiveModelsController::class, 'model']);
    Route::post('/predictions', [PredictiveModelsController::class, 'prediction']);
    Route::post('/evaluations', [PredictiveModelsController::class, 'evaluation']);
    Route::post('/drift', [PredictiveModelsController::class, 'drift']);
});
