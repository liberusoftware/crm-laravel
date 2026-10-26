<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\CRM\AIReceptionAndConversationApi\Http\Controllers\ReceptionController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/ai-reception-and-conversation')->group(function (): void {
    Route::get('agents', [ReceptionController::class, 'agents']);
    Route::post('agents', [ReceptionController::class, 'createAgent']);
    Route::post('agents/{agent}/activate', [ReceptionController::class, 'activate']);
    Route::post('agents/{agent}/conversations', [ReceptionController::class, 'start']);
    Route::post('conversations/{conversation}/turns', [ReceptionController::class, 'turn']);
    Route::post('conversations/{conversation}/handoff', [ReceptionController::class, 'handoff']);
});
