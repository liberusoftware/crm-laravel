<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\CaseManagementApi\Http\Controllers\CaseManagementController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/case-management')->group(function (): void {
    Route::get('cases', [CaseManagementController::class, 'index']);
    Route::post('cases', [CaseManagementController::class, 'store']);
    Route::post('cases/{case}/transition', [CaseManagementController::class, 'transition']);
    Route::get('queue', [CaseManagementController::class, 'queue']);
});
