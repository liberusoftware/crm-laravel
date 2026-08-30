<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\FeatureFlagsApi\Http\Controllers\StatusController;

Route::prefix('api/v1/feature-flags')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('feature-flags-api.status');
});
