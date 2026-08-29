<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\SaasPackagingApi\Http\Controllers\SaasController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/saas-packaging')->group(function (): void {
    Route::get('/plans', [SaasController::class, 'plans']);
    Route::get('/subscription', [SaasController::class, 'subscription']);
    Route::post('/subscription', [SaasController::class, 'provision']);
    Route::post('/subscription/{status}', [SaasController::class, 'status'])->whereIn('status', ['active', 'suspended', 'cancelled']);
    Route::get('/usage', [SaasController::class, 'usage']);
    Route::post('/usage', [SaasController::class, 'recordUsage']);
});
