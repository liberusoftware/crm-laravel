<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\CustomerSuccessApi\Http\Controllers\CustomerSuccessController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/customer-success')->group(function (): void {
    Route::get('customers', [CustomerSuccessController::class, 'index']);
    Route::post('customers', [CustomerSuccessController::class, 'store']);
    Route::post('customers/{customer}/signals', [CustomerSuccessController::class, 'signal']);
    Route::post('customers/{customer}/risks', [CustomerSuccessController::class, 'risk']);
    Route::post('customers/{customer}/renewals', [CustomerSuccessController::class, 'renewal']);
});
