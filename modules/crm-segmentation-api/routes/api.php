<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\SegmentationApi\Http\Controllers\SegmentationController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/segmentation')->group(function (): void {
    Route::get('/audiences', [SegmentationController::class, 'index']);
    Route::post('/audiences', [SegmentationController::class, 'store']);
    Route::patch('/audiences/{audience}', [SegmentationController::class, 'update']);
    Route::get('/audiences/{audience}/members', [SegmentationController::class, 'members']);
    Route::post('/audiences/{audience}/refresh', [SegmentationController::class, 'refresh']);
    Route::post('/events', [SegmentationController::class, 'event']);
});
