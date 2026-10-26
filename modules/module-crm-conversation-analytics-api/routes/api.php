<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\ConversationAnalyticsApi\Http\Controllers\ConversationAnalyticsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/conversation-analytics')->group(function (): void {
    Route::get('analyses', [ConversationAnalyticsController::class, 'index']);
    Route::post('analyses/{conversationKey}', [ConversationAnalyticsController::class, 'store']);
    Route::post('analyses/{analysis}/score', [ConversationAnalyticsController::class, 'score']);
    Route::get('trends', [ConversationAnalyticsController::class, 'trends']);
});
