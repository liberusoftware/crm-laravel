<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Filament\Resources\PredictionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\PredictiveModels\Actions\RecordPrediction;
use Liberu\CRM\PredictiveModels\Filament\Resources\PredictionResource;

final class CreatePrediction extends CreateRecord
{
    protected static string $resource = PredictionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(RecordPrediction::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
