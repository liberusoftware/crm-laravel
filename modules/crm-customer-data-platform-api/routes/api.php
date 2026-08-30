<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\CustomerDataPlatformApi\Http\Controllers\CdpController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/customer-data-platform')->group(function (): void {
    Route::get('profiles', [CdpController::class, 'profiles']);
    Route::post('profiles', [CdpController::class, 'storeProfile']);
    Route::post('profiles/{profile}/events', [CdpController::class, 'event']);
    Route::get('audiences', [CdpController::class, 'audiences']);
    Route::post('audiences', [CdpController::class, 'storeAudience']);
    Route::post('audiences/{audience}/activate', [CdpController::class, 'activate']);
});
