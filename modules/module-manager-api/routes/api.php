<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\ModuleManagerApi\Http\Controllers\StatusController;

Route::prefix('api/v1/module-manager')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('module-manager-api.status');
});
