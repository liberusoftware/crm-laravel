<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\MarketingDevelopmentFundsApi\Http\Controllers\MdfController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/marketing-development-funds')->group(function (): void {
    Route::get('/', [MdfController::class, 'index']);
    Route::post('/', [MdfController::class, 'store']);
    Route::post('{fund}/requests', [MdfController::class, 'request']);
    Route::post('requests/{request}/events', [MdfController::class, 'event']);
});
