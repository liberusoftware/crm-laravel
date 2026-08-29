<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\SchedulingApi\Http\Controllers\SchedulingController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/scheduling')->group(function (): void {
    Route::get('/links', [SchedulingController::class, 'links']);
    Route::post('/links', [SchedulingController::class, 'storeLink']);
    Route::get('/bookings', [SchedulingController::class, 'bookings']);
    Route::post('/bookings', [SchedulingController::class, 'storeBooking']);
    Route::post('/bookings/{booking}/{status}', [SchedulingController::class, 'status'])->whereIn('status', ['confirmed', 'rescheduled', 'cancelled', 'no_show']);
});
