<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\LoyaltyApi\Http\Controllers\LoyaltyController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/loyalty')->group(function (): void {
    Route::get('/', [LoyaltyController::class, 'index']);
    Route::post('/', [LoyaltyController::class, 'store']);
    Route::post('{program}/members', [LoyaltyController::class, 'enroll']);
    Route::post('members/{member}/points', [LoyaltyController::class, 'points']);
    Route::get('members/{member}/statement', [LoyaltyController::class, 'statement']);
});
