<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\CPQApi\Http\Controllers\CpqController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/cpq')->group(function (): void {
    Route::post('quotes', [CpqController::class, 'store']);
    Route::post('quotes/{quote}/submit', [CpqController::class, 'submit']);
});
