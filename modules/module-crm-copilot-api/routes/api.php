<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\CopilotApi\Http\Controllers\CopilotController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/crm-copilot')->group(function (): void {
    Route::post('ask', [CopilotController::class, 'ask']);
    Route::post('requests/{request}/actions', [CopilotController::class, 'propose']);
    Route::post('actions/{action}/confirm', [CopilotController::class, 'confirm']);
    Route::get('requests', [CopilotController::class, 'requests']);
});
