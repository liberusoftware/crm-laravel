<?php

declare(strict_types=1);
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Liberu\CRM\DialerAndOutreachApi\Http\Controllers\DialerController;

Route::middleware(['api', 'auth:sanctum'])->prefix('api/v1/crm/dialer-and-outreach')->group(function (): void {
    Route::get('lists', [DialerController::class, 'index']);
    Route::post('lists', [DialerController::class, 'store']);
    Route::patch('lists/{list}/state', [DialerController::class, 'state']);
    Route::post('lists/{list}/dispatch', [DialerController::class, 'dispatch']);
    Route::post('lists/{list}/calls', [DialerController::class, 'queue']);
    Route::post('calls/{call}/outcome', [DialerController::class, 'outcome']);
    Route::post('calls/{call}/retry', [DialerController::class, 'retry']);
});

Route::post('api/v1/crm/dialer/status/{call}', [DialerController::class, 'status'])->middleware(['signed', SubstituteBindings::class])->name('crm.dialer.status');
