<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\KnowledgeApi\Http\Controllers\KnowledgeController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/knowledge')->group(function (): void {
    Route::get('/', [KnowledgeController::class, 'index']);
    Route::post('/', [KnowledgeController::class, 'store']);
    Route::post('{article}/events', [KnowledgeController::class, 'event']);
});
