<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\OrganizationsTeamsApi\Http\Controllers\StatusController;

Route::prefix('api/v1/organizations-teams')->middleware('api')->group(function (): void {
    Route::get('/status', StatusController::class)->name('organizations-teams-api.status');
});
