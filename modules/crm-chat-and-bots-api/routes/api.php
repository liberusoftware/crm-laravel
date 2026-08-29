<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\ChatAndBotsApi\Http\Controllers\ChatAndBotsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/chat-and-bots')->group(function (): void {
    Route::get('bots', [ChatAndBotsController::class, 'bots']);
    Route::post('bots', [ChatAndBotsController::class, 'store']);
    Route::post('bots/{bot}/sessions', [ChatAndBotsController::class, 'session']);
    Route::post('sessions/{session}/handoff', [ChatAndBotsController::class, 'handoff']);
});
