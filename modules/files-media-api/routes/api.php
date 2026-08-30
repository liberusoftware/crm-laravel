<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\FilesMediaApi\Http\Controllers\StatusController;

Route::prefix('api/v1/files-media')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('files-media-api.status');
});
