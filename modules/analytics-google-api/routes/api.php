<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\AnalyticsGoogleApi\Http\Controllers\StatusController;

Route::prefix('api/v1/analytics-google')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('analytics-google-api.status');
});
