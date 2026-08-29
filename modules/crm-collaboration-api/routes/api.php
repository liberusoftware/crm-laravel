<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\CollaborationApi\Http\Controllers\CollaborationController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/collaboration')->group(function (): void {
    Route::get('records/{recordKey}', [CollaborationController::class, 'records']);
    Route::post('records', [CollaborationController::class, 'record']);
    Route::post('work', [CollaborationController::class, 'assign']);
    Route::post('work/{work}/handoff', [CollaborationController::class, 'handoff']);
    Route::get('queues/{queueKey}', [CollaborationController::class, 'queue']);
});
