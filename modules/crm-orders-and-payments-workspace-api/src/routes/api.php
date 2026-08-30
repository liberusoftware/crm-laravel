<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\OrdersAndPaymentsWorkspaceApi\Http\Controllers\OrdersPaymentsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/orders-and-payments-workspace')->group(function (): void {
    Route::get('/', [OrdersPaymentsController::class, 'index']);
    Route::post('/', [OrdersPaymentsController::class, 'store']);
    Route::post('{transaction}/events', [OrdersPaymentsController::class, 'event']);
});
