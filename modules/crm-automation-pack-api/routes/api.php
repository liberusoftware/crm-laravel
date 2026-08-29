<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\AutomationPackApi\Http\Controllers\AutomationPackController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/crm-automation-pack')->group(function (): void {
    Route::get('recipes', [AutomationPackController::class, 'index']);
    Route::post('recipes', [AutomationPackController::class, 'store']);
    Route::post('recipes/{recipe}/publish', [AutomationPackController::class, 'publish']);
    Route::post('recipes/{recipe}/simulate', [AutomationPackController::class, 'simulate']);
    Route::post('recipes/{recipe}/approve', [AutomationPackController::class, 'approve']);
    Route::post('recipes/{recipe}/enroll', [AutomationPackController::class, 'enroll']);
});
