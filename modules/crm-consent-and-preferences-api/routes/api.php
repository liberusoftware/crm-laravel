<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\ConsentAndPreferences\Api\Http\Controllers\ConsentController;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('api/v1/crm/consent-and-preferences')->group(function (): void {
    Route::get('/', [ConsentController::class, 'index']);
    Route::post('/', [ConsentController::class, 'store']);
    Route::get('/{consent}', [ConsentController::class, 'show']);
    Route::patch('/{consent}', [ConsentController::class, 'update']);
    Route::post('/{consent}/withdraw', [ConsentController::class, 'withdraw']);
    Route::post('/evaluate', [ConsentController::class, 'evaluate']);
});
