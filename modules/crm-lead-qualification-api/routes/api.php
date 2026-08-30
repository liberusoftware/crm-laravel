<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\CRM\LeadQualificationApi\Http\Controllers\LeadQualificationController;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('api/v1/crm/lead-qualification')->group(function (): void {
    Route::get('/', [LeadQualificationController::class, 'index']);
    Route::post('/', [LeadQualificationController::class, 'store']);
    Route::post('{lead}/score', [LeadQualificationController::class, 'score']);
    Route::post('{lead}/events', [LeadQualificationController::class, 'event']);
});
