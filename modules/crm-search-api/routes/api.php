<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\CrmSearchApi\Http\Controllers\CrmSearchController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/crm-search')->group(function (): void {
    Route::get('/', [CrmSearchController::class, 'search']);
    Route::post('index', [CrmSearchController::class, 'index']);
    Route::get('views', [CrmSearchController::class, 'views']);
    Route::post('views', [CrmSearchController::class, 'saveView']);
    Route::get('recents', [CrmSearchController::class, 'recents']);
    Route::post('recents', [CrmSearchController::class, 'recent']);
});
