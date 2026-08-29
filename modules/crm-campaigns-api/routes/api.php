<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\CampaignsApi\Http\Controllers\CampaignsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/campaigns')->group(function (): void {
    Route::get('/', [CampaignsController::class, 'index']);
    Route::post('/', [CampaignsController::class, 'store']);
    Route::post('{campaign}/events', [CampaignsController::class, 'event']);
    Route::get('roi', [CampaignsController::class, 'roi']);
});
