<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\CRM\AccountPlanningApi\Http\Controllers\AccountPlanningController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/account-planning')->group(function (): void {
    Route::get('records', [AccountPlanningController::class, 'index']);
    Route::post('records', [AccountPlanningController::class, 'store']);
    Route::patch('records/{record}', [AccountPlanningController::class, 'update']);
    Route::post('records/{record}/transition', [AccountPlanningController::class, 'transition']);
});
