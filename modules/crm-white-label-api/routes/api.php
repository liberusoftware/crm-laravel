<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\WhiteLabel\Api\Http\Controllers\WhiteLabelController;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('api/v1/crm/white-label')->group(function (): void {
    Route::get('/', [WhiteLabelController::class, 'show'])->name('crm.white-label.show');
    Route::patch('/', [WhiteLabelController::class, 'update'])->name('crm.white-label.update');
});
