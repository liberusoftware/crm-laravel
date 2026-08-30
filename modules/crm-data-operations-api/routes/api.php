<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\DataOperations\Api\Http\Controllers\DataOperationController;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('api/v1/crm/data-operations')->group(function (): void {
    Route::get('/', [DataOperationController::class, 'index']);
    Route::post('/', [DataOperationController::class, 'store']);
    Route::get('/{operation}', [DataOperationController::class, 'show']);
    Route::patch('/{operation}', [DataOperationController::class, 'update']);
    Route::post('/{operation}/transition', [DataOperationController::class, 'transition'])->middleware('throttle:api');
});
