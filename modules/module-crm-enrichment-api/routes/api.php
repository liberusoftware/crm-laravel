<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\EnrichmentApi\Http\Controllers\EnrichmentController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/enrichment')->group(function (): void {
    Route::get('profiles', [EnrichmentController::class, 'index']);
    Route::post('profiles', [EnrichmentController::class, 'store']);
    Route::post('profiles/{profile}/provenance', [EnrichmentController::class, 'provenance']);
    Route::post('profiles/{profile}/changes', [EnrichmentController::class, 'change']);
    Route::post('profiles/{profile}/verify', [EnrichmentController::class, 'verify']);
});
