<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\FeedbackAndVoiceOfCustomerApi\Http\Controllers\FeedbackController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/feedback-and-voice-of-customer')->group(function (): void {
    Route::get('surveys', [FeedbackController::class, 'index']);
    Route::post('surveys', [FeedbackController::class, 'store']);
    Route::post('surveys/{survey}/deliver', [FeedbackController::class, 'deliver']);
    Route::post('deliveries/{delivery}/responses', [FeedbackController::class, 'respond']);
    Route::get('surveys/{survey}/trend', [FeedbackController::class, 'trend']);
    Route::post('responses/{response}/cases', [FeedbackController::class, 'openCase']);
});
