<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\ClientOnboardingApi\Http\Controllers\ClientOnboardingController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/client-onboarding')->group(function (): void {
    Route::get('/', [ClientOnboardingController::class, 'index']);
    Route::post('/', [ClientOnboardingController::class, 'store']);
    Route::post('{onboarding}/steps', [ClientOnboardingController::class, 'step']);
});
