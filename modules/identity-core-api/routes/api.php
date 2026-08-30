<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\IdentityCoreApi\Http\Controllers\StatusController;

Route::prefix('api/v1/identity-core')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('identity-core-api.status');
});
