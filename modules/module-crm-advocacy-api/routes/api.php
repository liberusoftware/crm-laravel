<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\CRM\AdvocacyApi\Http\Controllers\AdvocacyController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/advocacy')->group(function (): void {
    Route::get('records', [AdvocacyController::class, 'index']);
    Route::post('records', [AdvocacyController::class, 'store']);
    Route::patch('records/{record}', [AdvocacyController::class, 'update']);
    Route::post('records/{record}/transition', [AdvocacyController::class, 'transition']);
});
