<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\SchedulerQueuesApi\Http\Controllers\StatusController;

Route::prefix('api/v1/scheduler-queues')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('scheduler-queues-api.status');
});
