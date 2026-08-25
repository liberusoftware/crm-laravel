<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\ProjectsApi\Http\Controllers\ProjectsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/projects')->group(function (): void {
    Route::get('/', [ProjectsController::class, 'projects']);
    Route::post('/', [ProjectsController::class, 'project']);
    Route::post('/tasks', [ProjectsController::class, 'task']);
    Route::post('/time', [ProjectsController::class, 'time']);
    Route::post('/risks', [ProjectsController::class, 'risk']);
    Route::post('/{project}/{status}', [ProjectsController::class, 'status'])->whereIn('status', ['planning', 'active', 'at_risk', 'on_hold', 'completed', 'cancelled']);
    Route::post('/{project}/handoff', [ProjectsController::class, 'handoff']);
});
