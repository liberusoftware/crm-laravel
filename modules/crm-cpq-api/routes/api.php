<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\CPQApi\Http\Controllers\CpqController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/cpq')->group(function (): void {
    Route::get('quotes', [CpqController::class, 'index']);
    Route::post('quotes', [CpqController::class, 'store']);
    Route::get('quotes/{quote}', [CpqController::class, 'show']);
    Route::post('quotes/{quote}/submit', [CpqController::class, 'submit']);
});
