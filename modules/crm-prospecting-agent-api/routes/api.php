<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\ProspectingAgentApi\Http\Controllers\ProspectingAgentController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/prospecting-agent')->group(function (): void {
    Route::get('/runs', [ProspectingAgentController::class, 'runs']);
    Route::post('/runs', [ProspectingAgentController::class, 'run']);
    Route::post('/runs/{run}/approve', [ProspectingAgentController::class, 'approve']);
    Route::post('/targets', [ProspectingAgentController::class, 'target']);
    Route::post('/sequences', [ProspectingAgentController::class, 'sequence']);
    Route::post('/sequences/{sequence}/dispatch', [ProspectingAgentController::class, 'dispatch']);
    Route::post('/engagements', [ProspectingAgentController::class, 'engagement']);
});
