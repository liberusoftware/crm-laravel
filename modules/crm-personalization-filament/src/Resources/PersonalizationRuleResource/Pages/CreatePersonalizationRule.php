<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Filament\Resources\PersonalizationRuleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Personalization\Actions\CreatePersonalizationRule as CreatePersonalizationRuleAction;
use Liberu\CRM\Personalization\Filament\Resources\PersonalizationRuleResource;

final class CreatePersonalizationRule extends CreateRecord
{
    protected static string $resource = PersonalizationRuleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreatePersonalizationRuleAction::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
