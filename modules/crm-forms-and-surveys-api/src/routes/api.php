<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\FormsAndSurveysApi\Http\Controllers\FormsController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/forms-and-surveys')->group(function (): void {
    Route::get('/', [FormsController::class, 'index']);
    Route::post('/', [FormsController::class, 'store']);
    Route::post('{form}/submissions', [FormsController::class, 'submit']);
    Route::post('submissions/{submission}/follow-up', [FormsController::class, 'followUp']);
});
