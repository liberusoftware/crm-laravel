<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\CRM\AffiliateManagementApi\Http\Controllers\AffiliateController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/affiliate-management')->group(function (): void {
    Route::get('affiliates', [AffiliateController::class, 'index']);
    Route::post('affiliates', [AffiliateController::class, 'store']);
    Route::post('affiliates/{affiliate}/approve', [AffiliateController::class, 'approve']);
    Route::post('affiliates/{affiliate}/links', [AffiliateController::class, 'link']);
    Route::post('links/{link}/events', [AffiliateController::class, 'event']);
    Route::get('events', [AffiliateController::class, 'events']);
});
