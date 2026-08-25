<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\ContractsApi\Http\Controllers\ContractsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/contracts')->group(function (): void {
    Route::get('/', [ContractsController::class, 'index']);
    Route::post('/', [ContractsController::class, 'store']);
    Route::post('{contract}/{type}', [ContractsController::class, 'transition']);
    Route::get('compliance', [ContractsController::class, 'compliance']);
});
