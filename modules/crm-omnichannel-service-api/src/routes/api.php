<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\OmnichannelServiceApi\Http\Controllers\OmnichannelController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/omnichannel-service')->group(function (): void {
    Route::get('/', [OmnichannelController::class, 'index']);
    Route::post('/', [OmnichannelController::class, 'store']);
    Route::post('{conversation}/interactions', [OmnichannelController::class, 'interaction']);
    Route::post('{conversation}/assign', [OmnichannelController::class, 'assign']);
    Route::post('{conversation}/workspace-events', [OmnichannelController::class, 'workspaceEvent']);
});
