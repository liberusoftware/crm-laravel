<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\JetstreamBridgeApi\Http\Controllers\StatusController;

Route::prefix('api/v1/jetstream-bridge')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('jetstream-bridge-api.status');
});
