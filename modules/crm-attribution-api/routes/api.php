<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\CRM\AttributionApi\Http\Controllers\AttributionController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/attribution')->group(function (): void {
    Route::get('touchpoints', [AttributionController::class, 'touchpoints']);
    Route::post('touchpoints', [AttributionController::class, 'recordTouchpoint']);
    Route::get('conversions', [AttributionController::class, 'conversions']);
    Route::post('conversions', [AttributionController::class, 'recordConversion']);
});
