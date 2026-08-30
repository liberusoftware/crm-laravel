<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Filament\Resources\PredictionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\PredictiveModels\Actions\UpdatePrediction;
use Liberu\CRM\PredictiveModels\Filament\Resources\PredictionResource;
use Liberu\CRM\PredictiveModels\Models\Prediction;

final class EditPrediction extends EditRecord
{
    protected static string $resource = PredictionResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Prediction, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdatePrediction::class)->execute((int) $teamId, auth()->id(), $record->id, $data);
    }
}
