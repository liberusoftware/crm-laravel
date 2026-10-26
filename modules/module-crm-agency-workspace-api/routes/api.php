<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\CRM\AgencyWorkspaceApi\Http\Controllers\AgencyWorkspaceController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/agency-workspace')->group(function (): void {
    Route::get('accounts', [AgencyWorkspaceController::class, 'index']);
    Route::post('accounts', [AgencyWorkspaceController::class, 'store']);
    Route::post('accounts/{account}/access', [AgencyWorkspaceController::class, 'grantAccess']);
    Route::post('accounts/{account}/usage', [AgencyWorkspaceController::class, 'usage']);
});
