<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\NotificationsApi\Http\Controllers\StatusController;

Route::prefix('api/v1/notifications')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('notifications-api.status');
});
