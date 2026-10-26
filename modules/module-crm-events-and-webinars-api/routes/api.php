<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\EventsAndWebinarsApi\Http\Controllers\EventsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/events-and-webinars')->group(function (): void {
    Route::get('/', [EventsController::class, 'index']);
    Route::post('/', [EventsController::class, 'store']);
    Route::post('{event}/registrations', [EventsController::class, 'register']);
    Route::post('registrations/{registration}/check-in', [EventsController::class, 'checkIn']);
    Route::get('{event}/attendance', [EventsController::class, 'attendance']);
    Route::post('{event}/follow-ups', [EventsController::class, 'followUp']);
});
