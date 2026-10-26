<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\EmailMarketingApi\Http\Controllers\EmailMarketingController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/email-marketing')->group(function (): void {
    Route::get('campaigns', [EmailMarketingController::class, 'index']);
    Route::post('campaigns', [EmailMarketingController::class, 'store']);
    Route::post('campaigns/{campaign}/schedule', [EmailMarketingController::class, 'schedule']);
    Route::post('campaigns/{campaign}/events', [EmailMarketingController::class, 'event']);
    Route::get('campaigns/{campaign}/analytics', [EmailMarketingController::class, 'analytics']);
});
