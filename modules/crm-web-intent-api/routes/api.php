<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\WebIntent\Api\Http\Controllers\WebIntentController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/web-intent')->group(function (): void {
    Route::get('/summary', [WebIntentController::class, 'summary'])->name('crm.web-intent.summary');
    Route::get('/alerts', [WebIntentController::class, 'alerts'])->name('crm.web-intent.alerts');
    Route::get('/visits', [WebIntentController::class, 'index'])->name('crm.web-intent.visits.index');
    Route::post('/visits', [WebIntentController::class, 'store'])->name('crm.web-intent.visits.store');
    Route::get('/visits/{visit}', [WebIntentController::class, 'show'])->name('crm.web-intent.visits.show');
    Route::post('/visits/{visit}/engagements', [WebIntentController::class, 'engagement'])->name('crm.web-intent.engagements.store');
    Route::post('/consents', [WebIntentController::class, 'consent'])->name('crm.web-intent.consents.store');
    Route::post('/identifications', [WebIntentController::class, 'identification'])->name('crm.web-intent.identifications.store');
    Route::post('/alerts', [WebIntentController::class, 'createAlert'])->name('crm.web-intent.alerts.store');
    Route::post('/alerts/{alert}/resolve', [WebIntentController::class, 'resolveAlert'])->name('crm.web-intent.alerts.resolve');
    Route::post('/conversions', [WebIntentController::class, 'convert'])->name('crm.web-intent.conversions.store');
});
