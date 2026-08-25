<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\SalesWorkspaceApi\Http\Controllers\WorkspaceController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/sales-workspace')->group(function (): void {
    Route::get('/feed', [WorkspaceController::class, 'feed']);
    Route::get('/overdue', [WorkspaceController::class, 'overdue']);
    Route::get('/agenda', [WorkspaceController::class, 'agenda']);
    Route::post('/items', [WorkspaceController::class, 'store']);
    Route::patch('/items/{item}', [WorkspaceController::class, 'update']);
});
