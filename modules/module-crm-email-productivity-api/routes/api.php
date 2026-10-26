<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\EmailProductivityApi\Http\Controllers\EmailProductivityController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/email-productivity')->group(function (): void {
    Route::get('mailboxes', [EmailProductivityController::class, 'mailboxes']);
    Route::get('templates', [EmailProductivityController::class, 'templates']);
    Route::get('messages', [EmailProductivityController::class, 'messages']);
    Route::post('mailboxes', [EmailProductivityController::class, 'connect']);
    Route::post('templates', [EmailProductivityController::class, 'template']);
    Route::post('messages', [EmailProductivityController::class, 'send']);
    Route::post('messages/{message}/events', [EmailProductivityController::class, 'event']);
});
