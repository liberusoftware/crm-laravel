<?php

declare(strict_types=1);

namespace Liberu\CRM\AttributionFilament\Resources\TouchpointResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Attribution\Actions\RecordTouchpoint;
use Liberu\CRM\AttributionFilament\Resources\TouchpointResource;

final class CreateTouchpoint extends CreateRecord
{
    protected static string $resource = TouchpointResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()?->getAttribute('current_team_id');
        abort_unless(is_numeric($teamId) && (int) $teamId > 0, 403);

        return app(RecordTouchpoint::class)->execute((int) $teamId, $data);
    }
}
