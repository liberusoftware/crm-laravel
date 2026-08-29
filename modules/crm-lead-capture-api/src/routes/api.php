<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\LeadCaptureApi\Http\Controllers\LeadCaptureController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/lead-capture')->group(function (): void {
    Route::get('/', [LeadCaptureController::class, 'index']);
    Route::post('/', [LeadCaptureController::class, 'store']);
    Route::post('{lead}/events', [LeadCaptureController::class, 'event']);
});
