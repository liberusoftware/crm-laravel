<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\ProspectingApi\Http\Controllers\ProspectingController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/prospecting')->group(function (): void {
    Route::get('/profiles', [ProspectingController::class, 'profiles']);
    Route::post('/profiles', [ProspectingController::class, 'profile']);
    Route::get('/searches', [ProspectingController::class, 'searches']);
    Route::post('/searches', [ProspectingController::class, 'search']);
    Route::get('/prospects', [ProspectingController::class, 'prospects']);
    Route::post('/prospects', [ProspectingController::class, 'prospect']);
    Route::post('/research', [ProspectingController::class, 'research']);
    Route::post('/reveal', [ProspectingController::class, 'reveal']);
    Route::post('/exports', [ProspectingController::class, 'export']);
});
