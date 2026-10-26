<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\ContactCenterApi\Http\Controllers\ContactCenterController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/contact-center')->group(function (): void {
    Route::get('agents', [ContactCenterController::class, 'agents']);
    Route::post('presence', [ContactCenterController::class, 'presence']);
    Route::post('route', [ContactCenterController::class, 'route']);
    Route::get('supervisor', [ContactCenterController::class, 'supervisor']);
});
