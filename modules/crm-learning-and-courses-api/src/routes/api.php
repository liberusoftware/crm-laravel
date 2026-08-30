<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\CRM\LearningAndCoursesApi\Http\Controllers\LearningController;

Route::middleware('auth:sanctum')->prefix('api/v1/crm/learning-and-courses')->group(function (): void {
    Route::get('/', [LearningController::class, 'index']);
    Route::post('/', [LearningController::class, 'store']);
    Route::post('{course}/enrollments', [LearningController::class, 'enroll']);
    Route::post('enrollments/{enrollment}/records', [LearningController::class, 'record']);
    Route::get('enrollments/{enrollment}/records', [LearningController::class, 'records']);
});
