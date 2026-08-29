<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\RoutingApi\Http\Controllers\RoutingController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/routing')->group(function (): void {
    Route::get('/rules', [RoutingController::class, 'rules']);
    Route::post('/rules', [RoutingController::class, 'rule']);
    Route::get('/agents', [RoutingController::class, 'agents']);
    Route::post('/agents', [RoutingController::class, 'agent']);
    Route::get('/assignments', [RoutingController::class, 'assignments']);
    Route::post('/assignments', [RoutingController::class, 'assign']);
    Route::post('/assignments/{assignment}/{status}', [RoutingController::class, 'status'])->whereIn('status', ['accepted', 'rejected', 'expired']);
});
