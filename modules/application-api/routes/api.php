<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\ApplicationApi\Http\Controllers\StatusController;

Route::prefix('api/v1/application')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('application-api.status');
});
