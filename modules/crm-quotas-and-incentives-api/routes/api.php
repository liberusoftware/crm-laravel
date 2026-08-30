<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\QuotasAndIncentivesApi\Http\Controllers\QuotasAndIncentivesController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/quotas-and-incentives')->group(function (): void {
    Route::get('/quotas', [QuotasAndIncentivesController::class, 'quotas']);
    Route::post('/quotas', [QuotasAndIncentivesController::class, 'quota']);
    Route::get('/plans', [QuotasAndIncentivesController::class, 'plans']);
    Route::post('/plans', [QuotasAndIncentivesController::class, 'plan']);
    Route::get('/credits', [QuotasAndIncentivesController::class, 'credits']);
    Route::post('/credits', [QuotasAndIncentivesController::class, 'credit']);
    Route::post('/disputes', [QuotasAndIncentivesController::class, 'dispute']);
    Route::post('/disputes/{dispute}/resolve', [QuotasAndIncentivesController::class, 'resolve']);
    Route::post('/exports', [QuotasAndIncentivesController::class, 'export']);
});
