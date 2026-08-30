<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\AnalyticsCoreApi\Http\Controllers\StatusController;

Route::prefix('api/v1/analytics-core')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('analytics-core-api.status');
});
