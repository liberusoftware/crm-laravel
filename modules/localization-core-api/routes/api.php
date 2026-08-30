<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\LocalizationCoreApi\Http\Controllers\StatusController;

Route::prefix('api/v1/localization-core')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('localization-core-api.status');
});
