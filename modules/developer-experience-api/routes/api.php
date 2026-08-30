<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\DeveloperExperienceApi\Http\Controllers\StatusController;

Route::prefix('api/v1/developer-experience')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('developer-experience-api.status');
});
