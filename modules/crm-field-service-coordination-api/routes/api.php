<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\FieldServiceCoordinationApi\Http\Controllers\FieldServiceController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/field-service-coordination')->group(function (): void {
    Route::get('work-types', [FieldServiceController::class, 'workTypes']);
    Route::get('appointments', [FieldServiceController::class, 'appointments']);
    Route::post('work-types', [FieldServiceController::class, 'storeWorkType']);
    Route::post('appointments', [FieldServiceController::class, 'schedule']);
    Route::post('appointments/{appointment}/history', [FieldServiceController::class, 'history']);
    Route::post('appointments/{appointment}/maintenance-handoff', [FieldServiceController::class, 'handoff']);
});
