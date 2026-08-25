<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\CRM\AccountBasedMarketingApi\Http\Controllers\AccountBasedMarketingController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/account-based-marketing')->group(function (): void {
    Route::get('records', [AccountBasedMarketingController::class, 'index']);
    Route::post('records', [AccountBasedMarketingController::class, 'store']);
    Route::patch('records/{record}', [AccountBasedMarketingController::class, 'update']);
    Route::post('records/{record}/transition', [AccountBasedMarketingController::class, 'transition']);
});
