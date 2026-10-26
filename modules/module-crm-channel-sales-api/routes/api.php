<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\ChannelSalesApi\Http\Controllers\ChannelSalesController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/channel-sales')->group(function (): void {
    Route::get('opportunities', [ChannelSalesController::class, 'index']);
    Route::post('opportunities', [ChannelSalesController::class, 'store']);
    Route::post('opportunities/{opportunity}/advance', [ChannelSalesController::class, 'advance']);
    Route::get('forecast', [ChannelSalesController::class, 'forecast']);
});
