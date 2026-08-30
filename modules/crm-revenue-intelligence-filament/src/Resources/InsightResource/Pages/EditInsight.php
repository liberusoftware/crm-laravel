<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Filament\Resources\InsightResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\RevenueIntelligence\Actions\UpdateInsight;
use Liberu\CRM\RevenueIntelligence\Filament\Resources\InsightResource;
use Liberu\CRM\RevenueIntelligence\Models\RevenueInsight;

final class EditInsight extends EditRecord
{
    protected static string $resource = InsightResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof RevenueInsight, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdateInsight::class)->execute((int) $teamId, auth()->id(), $record->id, $data);
    }
}
