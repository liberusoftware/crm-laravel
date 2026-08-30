<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\ProfilesApi\Http\Controllers\StatusController;

Route::prefix('api/v1/profiles')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('profiles-api.status');
});
