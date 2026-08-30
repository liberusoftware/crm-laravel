<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\ChannelGatewayApi\Http\Controllers\ChannelGatewayController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/channel-gateway')->group(function (): void {
    Route::get('channels', [ChannelGatewayController::class, 'index']);
    Route::post('channels', [ChannelGatewayController::class, 'store']);
    Route::post('channels/{channel}/deliveries', [ChannelGatewayController::class, 'delivery']);
    Route::post('channels/{channel}/health', [ChannelGatewayController::class, 'health']);
});
