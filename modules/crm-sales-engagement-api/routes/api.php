<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\SalesEngagementApi\Http\Controllers\EngagementController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/sales-engagement')->group(function (): void {
    Route::get('/sequences', [EngagementController::class, 'sequences']);
    Route::post('/sequences', [EngagementController::class, 'sequence']);
    Route::post('/steps', [EngagementController::class, 'step']);
    Route::get('/enrollments', [EngagementController::class, 'enrollments']);
    Route::post('/enrollments', [EngagementController::class, 'enroll']);
    Route::post('/enrollments/{enrollment}/stop/{reason}', [EngagementController::class, 'stop']);
    Route::post('/events', [EngagementController::class, 'event']);
});
