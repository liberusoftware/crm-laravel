<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\Core\Api\Http\Controllers\RecordController;

Route::prefix('api/v1/crm/crm-core')->middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
    Route::get('/', [RecordController::class, 'index'])->name('crm.core.list');
    Route::post('/', [RecordController::class, 'store'])->name('crm.core.create');
    Route::get('/{record}', [RecordController::class, 'show'])->name('crm.core.get');
    Route::patch('/{record}', [RecordController::class, 'update'])->name('crm.core.update');
    Route::delete('/{record}', [RecordController::class, 'destroy'])->name('crm.core.delete');
});
