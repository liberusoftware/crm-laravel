<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\RolesPermissionsApi\Http\Controllers\StatusController;

Route::prefix('api/v1/roles-permissions')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('roles-permissions-api.status');
});
