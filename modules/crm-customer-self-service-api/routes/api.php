<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\CustomerSelfServiceApi\Http\Controllers\SelfServiceController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/customer-self-service')->group(function (): void {
    Route::get('profile', [SelfServiceController::class, 'profile']);
    Route::put('profile', [SelfServiceController::class, 'storeProfile']);
    Route::put('profile/preferences', [SelfServiceController::class, 'preferences']);
    Route::get('cases', [SelfServiceController::class, 'cases']);
    Route::post('cases', [SelfServiceController::class, 'submitCase']);
    Route::get('knowledge', [SelfServiceController::class, 'knowledge']);
});
