<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\SandboxAndReleaseManagementApi\Http\Controllers\ReleaseController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/sandbox-and-release-management')->group(function (): void {
    Route::get('/snapshots', [ReleaseController::class, 'snapshots']);
    Route::post('/snapshots', [ReleaseController::class, 'snapshot']);
    Route::get('/changesets', [ReleaseController::class, 'changesets']);
    Route::post('/changesets', [ReleaseController::class, 'changeset']);
    Route::post('/changesets/{set}/validate', [ReleaseController::class, 'validateSet']);
    Route::post('/changesets/{set}/promote', [ReleaseController::class, 'promote']);
    Route::post('/changesets/{set}/rollback', [ReleaseController::class, 'rollback']);
});
