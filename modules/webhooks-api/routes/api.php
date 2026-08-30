<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\WebhooksApi\Http\Controllers\StatusController;

Route::prefix('api/v1/webhooks')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('webhooks-api.status');
});
