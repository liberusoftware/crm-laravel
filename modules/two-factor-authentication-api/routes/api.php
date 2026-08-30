<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\TwoFactorAuthenticationApi\Http\Controllers\StatusController;

Route::prefix('api/v1/two-factor-authentication')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('two-factor-authentication-api.status');
});
