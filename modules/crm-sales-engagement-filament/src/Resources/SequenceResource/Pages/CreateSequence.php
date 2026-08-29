<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Filament\Resources\SequenceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\SalesEngagement\Actions\CreateSequence as CreateSequenceAction;
use Liberu\CRM\SalesEngagement\Filament\Resources\SequenceResource;

final class CreateSequence extends CreateRecord
{
    protected static string $resource = SequenceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateSequenceAction::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
