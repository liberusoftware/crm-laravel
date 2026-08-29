<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Filament\Resources\InsightResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\RevenueIntelligence\Actions\RecordInsight;
use Liberu\CRM\RevenueIntelligence\Filament\Resources\InsightResource;

final class CreateInsight extends CreateRecord
{
    protected static string $resource = InsightResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(RecordInsight::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
