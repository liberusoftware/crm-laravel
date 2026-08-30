<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\AuditApi\Http\Controllers\StatusController;

Route::prefix('api/v1/audit')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('audit-api.status');
});
