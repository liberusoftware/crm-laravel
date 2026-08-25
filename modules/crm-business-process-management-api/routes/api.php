<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\CRM\BusinessProcessManagementApi\Http\Controllers\BusinessProcessesController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/business-process-management')->group(function (): void {
    Route::get('/', [BusinessProcessesController::class, 'index']);
    Route::post('/', [BusinessProcessesController::class, 'store']);
    Route::post('{process}/publish', [BusinessProcessesController::class, 'publish']);
    Route::post('{process}/runs', [BusinessProcessesController::class, 'start']);
    Route::post('runs/{run}/advance', [BusinessProcessesController::class, 'advance']);
    Route::get('runs', [BusinessProcessesController::class, 'runs']);
});
