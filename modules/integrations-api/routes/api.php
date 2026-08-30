<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\IntegrationsApi\Http\Controllers\StatusController;

Route::prefix('api/v1/integrations')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('integrations-api.status');
});
