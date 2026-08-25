<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\MembershipsApi\Http\Controllers\MembershipsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/memberships')->group(function (): void {
    Route::get('/', [MembershipsController::class, 'index']);
    Route::post('/', [MembershipsController::class, 'store']);
    Route::post('{plan}/grants', [MembershipsController::class, 'grant']);
    Route::post('grants/{grant}/status', [MembershipsController::class, 'status']);
});
