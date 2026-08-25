<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\PartnerRelationshipManagementApi\Http\Controllers\PartnerRelationshipManagementController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/partner-relationship-management')->group(function (): void {
    Route::get('/partners', [PartnerRelationshipManagementController::class, 'partners']);
    Route::post('/partners', [PartnerRelationshipManagementController::class, 'partner']);
    Route::post('/contacts', [PartnerRelationshipManagementController::class, 'contact']);
    Route::post('/partners/{partner}/{status}', [PartnerRelationshipManagementController::class, 'status'])->whereIn('status', ['prospect', 'onboarding', 'active', 'suspended', 'inactive']);
    Route::post('/activities', [PartnerRelationshipManagementController::class, 'activity']);
    Route::post('/performance', [PartnerRelationshipManagementController::class, 'performance']);
});
