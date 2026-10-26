<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\CRM\AdvertisingApi\Http\Controllers\AdvertisingController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/advertising')->group(function (): void {
    Route::get('records', [AdvertisingController::class, 'index']);
    Route::post('records', [AdvertisingController::class, 'store']);
    Route::get('records/{record}', [AdvertisingController::class, 'show']);
    Route::patch('records/{record}', [AdvertisingController::class, 'update']);
    Route::post('records/{record}/transition', [AdvertisingController::class, 'transition']);
    Route::delete('records/{record}', [AdvertisingController::class, 'destroy']);
});
