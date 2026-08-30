<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\UnifiedConversationsApi\Http\Controllers\ConversationController;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('api/v1/crm/unified-conversations')->group(function () {
    Route::get('/', [ConversationController::class, 'index']);
    Route::post('/', [ConversationController::class, 'store']);
    Route::post('/{conversation}/messages', [ConversationController::class, 'message']);
});
