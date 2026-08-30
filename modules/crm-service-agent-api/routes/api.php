<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\ServiceAgentApi\Http\Controllers\AgentController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/service-agent')->group(function (): void {
    Route::get('/cases', [AgentController::class, 'cases']);
    Route::post('/cases', [AgentController::class, 'storeCase']);
    Route::post('/cases/{case}/classify', [AgentController::class, 'classify']);
    Route::post('/cases/{case}/output/{type}', [AgentController::class, 'output'])->whereIn('type', ['draft', 'plan']);
    Route::post('/cases/{case}/escalate', [AgentController::class, 'escalate']);
    Route::get('/knowledge', [AgentController::class, 'knowledge']);
    Route::post('/tools', [AgentController::class, 'tool']);
    Route::post('/reviews', [AgentController::class, 'review']);
});
