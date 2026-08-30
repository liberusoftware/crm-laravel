<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\MobileMessagingApi\Http\Controllers\MobileMessagingController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/mobile-messaging')->group(function (): void {
    Route::get('/', [MobileMessagingController::class, 'index']);
    Route::post('contacts/consent', [MobileMessagingController::class, 'consent']);
    Route::post('/', [MobileMessagingController::class, 'store']);
    Route::post('{campaign}/messages', [MobileMessagingController::class, 'message']);
});
