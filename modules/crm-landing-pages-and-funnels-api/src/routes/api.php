<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\LandingPagesAndFunnelsApi\Http\Controllers\FunnelController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/landing-pages-and-funnels')->group(function (): void {
    Route::get('/', [FunnelController::class, 'index']);
    Route::post('/', [FunnelController::class, 'store']);
    Route::post('{funnel}/pages', [FunnelController::class, 'page']);
    Route::post('{funnel}/publish', [FunnelController::class, 'publish']);
    Route::post('{funnel}/events', [FunnelController::class, 'event']);
});
