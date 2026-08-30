<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\SalesPipelinesApi\Http\Controllers\PipelineController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/sales-pipelines')->group(function (): void {
    Route::get('/pipelines', [PipelineController::class, 'pipelines']);
    Route::post('/pipelines', [PipelineController::class, 'pipeline']);
    Route::post('/stages', [PipelineController::class, 'stage']);
    Route::get('/opportunities', [PipelineController::class, 'opportunities']);
    Route::post('/opportunities', [PipelineController::class, 'opportunity']);
    Route::post('/opportunities/{opportunity}/move', [PipelineController::class, 'move']);
    Route::post('/opportunities/{opportunity}/close/{status}', [PipelineController::class, 'close'])->whereIn('status', ['won', 'lost']);
});
