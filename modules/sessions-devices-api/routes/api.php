<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\SessionsDevicesApi\Http\Controllers\StatusController;

Route::prefix('api/v1/sessions-devices')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('sessions-devices-api.status');
});
