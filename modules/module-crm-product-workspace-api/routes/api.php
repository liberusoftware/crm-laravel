<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\ProductWorkspaceApi\Http\Controllers\ProductWorkspaceController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/crm-product-workspace')->group(function (): void {
    Route::get('products', [ProductWorkspaceController::class, 'products']);
    Route::post('products', [ProductWorkspaceController::class, 'store']);
    Route::post('entitlements', [ProductWorkspaceController::class, 'entitlement']);
    Route::post('syncs', [ProductWorkspaceController::class, 'sync']);
});
