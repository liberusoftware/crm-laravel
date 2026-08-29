<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Filament\Resources\ReputationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\ReputationManagement\Actions\RecordReview;
use Liberu\CRM\ReputationManagement\Filament\Resources\ReputationResource;

final class CreateReview extends CreateRecord
{
    protected static string $resource = ReputationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(RecordReview::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
