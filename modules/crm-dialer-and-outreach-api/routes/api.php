<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\DialerAndOutreachApi\Http\Controllers\DialerController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/dialer-and-outreach')->group(function (): void {
    Route::get('lists', [DialerController::class, 'index']);
    Route::post('lists', [DialerController::class, 'store']);
    Route::post('lists/{list}/calls', [DialerController::class, 'queue']);
    Route::post('calls/{call}/outcome', [DialerController::class, 'outcome']);
    Route::post('calls/{call}/retry', [DialerController::class, 'retry']);
});
