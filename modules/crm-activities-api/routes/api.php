<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\Activities\Api\Http\Controllers\ActivityController;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('api/v1/crm/activities')->group(function (): void {
    Route::get('/', [ActivityController::class, 'index']);
    Route::post('/', [ActivityController::class, 'store']);
    Route::get('/report', [ActivityController::class, 'report']);
    Route::post('/complete', [ActivityController::class, 'complete']);
    Route::get('/{activity}', [ActivityController::class, 'show']);
    Route::patch('/{activity}', [ActivityController::class, 'update']);
    Route::post('/{activity}/cancel', [ActivityController::class, 'cancel']);
});
