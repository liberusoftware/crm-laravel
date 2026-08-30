<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\CRM\WorkManagement\Api\Http\Controllers\WorkManagementController;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('api/v1/crm/work-management')->group(function (): void {
    Route::get('/', [WorkManagementController::class, 'index']);
    Route::post('/', [WorkManagementController::class, 'store']);
    Route::get('/workload', [WorkManagementController::class, 'workload']);
    Route::get('/queues', [WorkManagementController::class, 'queues']);
    Route::post('/queues', [WorkManagementController::class, 'storeQueue']);
    Route::get('/{workItem}', [WorkManagementController::class, 'show']);
    Route::patch('/{workItem}', [WorkManagementController::class, 'update']);
    Route::post('/{workItem}/complete', [WorkManagementController::class, 'complete']);
    Route::post('/{workItem}/checklist', [WorkManagementController::class, 'checklist']);
    Route::post('/{workItem}/approvals', [WorkManagementController::class, 'requestApproval']);
    Route::post('/{workItem}/dependencies', [WorkManagementController::class, 'dependency']);
});
