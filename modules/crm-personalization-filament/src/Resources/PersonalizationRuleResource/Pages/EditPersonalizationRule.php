<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Filament\Resources\PersonalizationRuleResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Personalization\Actions\UpdatePersonalizationRule;
use Liberu\CRM\Personalization\Filament\Resources\PersonalizationRuleResource;
use Liberu\CRM\Personalization\Models\PersonalizationRule;

final class EditPersonalizationRule extends EditRecord
{
    protected static string $resource = PersonalizationRuleResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof PersonalizationRule, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdatePersonalizationRule::class)->execute((int) $teamId, auth()->id(), $record->id, $data);
    }
}
