<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\ProposalsAndQuotesApi\Http\Controllers\ProposalsAndQuotesController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/proposals-and-quotes')->group(function (): void {
    Route::get('/templates', [ProposalsAndQuotesController::class, 'templates']);
    Route::post('/templates', [ProposalsAndQuotesController::class, 'template']);
    Route::get('/proposals', [ProposalsAndQuotesController::class, 'proposals']);
    Route::post('/proposals', [ProposalsAndQuotesController::class, 'proposal']);
    Route::post('/versions', [ProposalsAndQuotesController::class, 'version']);
    Route::post('/proposals/{proposal}/{status}', [ProposalsAndQuotesController::class, 'status'])->whereIn('status', ['approved', 'delivered', 'accepted', 'rejected', 'expired']);
    Route::post('/comments', [ProposalsAndQuotesController::class, 'comment']);
});
