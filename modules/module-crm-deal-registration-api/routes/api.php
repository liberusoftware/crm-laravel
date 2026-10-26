<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\DealRegistrationApi\Http\Controllers\DealRegistrationController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/deal-registration')->group(function (): void {
    Route::get('deals', [DealRegistrationController::class, 'index']);
    Route::post('deals', [DealRegistrationController::class, 'store']);
    Route::post('deals/{deal}/approve', [DealRegistrationController::class, 'approve']);
    Route::post('deals/{deal}/collaborators', [DealRegistrationController::class, 'collaborate']);
});
