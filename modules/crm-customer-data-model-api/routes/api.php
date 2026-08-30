<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\CustomerDataModel\Api\Http\Controllers\ObjectDefinitionController;

Route::prefix('api/v1/crm/customer-data-model')->middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
    Route::get('/', [ObjectDefinitionController::class, 'index'])->name('crm.customer.data.model.list');
    Route::post('/', [ObjectDefinitionController::class, 'store'])->name('crm.customer.data.model.create');
    Route::get('/{object}', [ObjectDefinitionController::class, 'show'])->name('crm.customer.data.model.get');
    Route::patch('/{object}', [ObjectDefinitionController::class, 'update'])->name('crm.customer.data.model.update');
    Route::post('/{object}/publish', [ObjectDefinitionController::class, 'publish'])->name('crm.customer.data.model.publish');
});
