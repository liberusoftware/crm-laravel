<?php

use Illuminate\Support\Facades\Route;
use Liberu\CRM\Core\Api\Http\Controllers\RecordController;

Route::prefix('api/v1/crm/crm-core')->middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
    Route::get('/', [RecordController::class, 'index'])->name('crm.core.list');
    Route::post('/', [RecordController::class, 'store'])->name('crm.core.create');
    Route::get('/tags', [RecordController::class, 'tags'])->name('crm.core.tags.list');
    Route::post('/tags', [RecordController::class, 'createTag'])->name('crm.core.tags.create');
    Route::get('/{record}', [RecordController::class, 'show'])->name('crm.core.get');
    Route::patch('/{record}', [RecordController::class, 'update'])->name('crm.core.update');
    Route::get('/{record}/timeline', [RecordController::class, 'timeline'])->name('crm.core.timeline');
    Route::post('/{record}/notes', [RecordController::class, 'addNote'])->name('crm.core.notes.create');
    Route::post('/{record}/relationships', [RecordController::class, 'relationship'])->name('crm.core.relationships.create');
    Route::post('/{record}/tags/{tag}', [RecordController::class, 'tag'])->name('crm.core.tags.attach');
    Route::post('/{record}/favorite', [RecordController::class, 'favorite'])->name('crm.core.favorite');
    Route::delete('/{record}', [RecordController::class, 'destroy'])->name('crm.core.delete');
});
