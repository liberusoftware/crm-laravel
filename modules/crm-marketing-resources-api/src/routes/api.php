<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\MarketingResourcesApi\Http\Controllers\MarketingResourcesController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/marketing-resources')->group(function (): void {
    Route::get('/', [MarketingResourcesController::class, 'index']);
    Route::post('/', [MarketingResourcesController::class, 'store']);
    Route::post('{resource}/events', [MarketingResourcesController::class, 'event']);
});
