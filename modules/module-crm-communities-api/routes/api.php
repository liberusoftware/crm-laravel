<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\CommunitiesApi\Http\Controllers\CommunitiesController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/communities')->group(function (): void {
    Route::get('spaces', [CommunitiesController::class, 'index']);
    Route::post('spaces', [CommunitiesController::class, 'store']);
    Route::post('spaces/{space}/join', [CommunitiesController::class, 'join']);
    Route::post('spaces/{space}/content', [CommunitiesController::class, 'content']);
    Route::get('spaces/{space}/feed', [CommunitiesController::class, 'feed']);
});
