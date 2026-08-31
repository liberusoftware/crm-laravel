<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\TemplatesAndSnapshotsApi\Http\Controllers\SnapshotController;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('api/v1/crm/templates-and-snapshots')->group(function () {
    Route::get('/', [SnapshotController::class, 'index']);
    Route::post('/', [SnapshotController::class, 'store']);
    Route::get('/{snapshot}', [SnapshotController::class, 'show']);
    Route::patch('/{snapshot}', [SnapshotController::class, 'update']);
    Route::get('/{snapshot}/preview', [SnapshotController::class, 'preview']);
    Route::post('/{snapshot}/install', [SnapshotController::class, 'install']);
    Route::post('/{snapshot}/rollback', [SnapshotController::class, 'rollback']);
    Route::post('/{snapshot}/share', [SnapshotController::class, 'share']);
});
