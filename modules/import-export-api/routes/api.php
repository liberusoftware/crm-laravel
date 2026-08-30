<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\ImportExportApi\Http\Controllers\StatusController;

Route::prefix('api/v1/import-export')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('import-export-api.status');
});
