<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\MarketingAgentApi\Http\Controllers\MarketingAgentController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/marketing-agent')->group(function (): void {
    Route::get('/', [MarketingAgentController::class, 'index']);
    Route::post('/', [MarketingAgentController::class, 'store']);
    Route::post('{request}/checks', [MarketingAgentController::class, 'check']);
    Route::post('{request}/experiments', [MarketingAgentController::class, 'experiment']);
});
