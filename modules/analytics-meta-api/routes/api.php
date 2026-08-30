<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\AnalyticsMetaApi\Http\Controllers\StatusController;

Route::prefix('api/v1/analytics-meta')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('analytics-meta-api.status');
});
