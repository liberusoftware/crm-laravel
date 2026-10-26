<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\DocumentsApi\Http\Controllers\DocumentsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/crm-documents')->group(function (): void {
    Route::get('/', [DocumentsController::class, 'index']);
    Route::post('/', [DocumentsController::class, 'store']);
    Route::post('{document}/versions', [DocumentsController::class, 'version']);
    Route::post('{document}/links', [DocumentsController::class, 'link']);
    Route::post('{document}/engagement', [DocumentsController::class, 'engagement']);
});
