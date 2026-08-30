<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\UsageWalletAndRebillingApi\Http\Controllers\UsageWalletController;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('api/v1/crm/usage-wallet-and-rebilling')->group(function () {
    Route::get('/summary', [UsageWalletController::class, 'summary']);
    Route::get('/imports', [UsageWalletController::class, 'imports']);
    Route::post('/imports', [UsageWalletController::class, 'import']);
    Route::get('/charges', [UsageWalletController::class, 'charges']);
    Route::post('/charges', [UsageWalletController::class, 'charge']);
    Route::put('/wallet', [UsageWalletController::class, 'wallet']);
});
