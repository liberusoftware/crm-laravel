<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\ConversationIntelligenceApi\Http\Controllers\ConversationIntelligenceController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/conversation-intelligence')->group(function (): void {
    Route::get('conversations', [ConversationIntelligenceController::class, 'index']);
    Route::post('conversations', [ConversationIntelligenceController::class, 'store']);
    Route::post('conversations/{conversation}/analyze', [ConversationIntelligenceController::class, 'analyze']);
    Route::post('conversations/{conversation}/evidence', [ConversationIntelligenceController::class, 'evidence']);
    Route::get('evidence/search', [ConversationIntelligenceController::class, 'search']);
});
