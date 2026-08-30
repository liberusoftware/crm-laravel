<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\ApiAccessApi\Http\Controllers\StatusController;

Route::prefix('api/v1/api-access')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('api-access-api.status');
});
