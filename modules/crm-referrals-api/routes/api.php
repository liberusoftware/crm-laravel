<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\ReferralsApi\Http\Controllers\ReferralsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/referrals')->group(function (): void {
    Route::get('/programs', [ReferralsController::class, 'programs']);
    Route::post('/programs', [ReferralsController::class, 'program']);
    Route::get('/referrals', [ReferralsController::class, 'referrals']);
    Route::post('/referrals', [ReferralsController::class, 'referral']);
    Route::post('/referrals/{referral}/{status}', [ReferralsController::class, 'qualify'])->whereIn('status', ['qualified', 'rejected', 'converted']);
    Route::get('/rewards', [ReferralsController::class, 'rewards']);
    Route::post('/rewards', [ReferralsController::class, 'reward']);
});
