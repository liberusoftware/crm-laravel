<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Filament\Resources\ActivityGoalResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\Activities\Filament\Resources\ActivityGoalResource;

final class EditActivityGoal extends EditRecord
{
    protected static string $resource = ActivityGoalResource::class;
}
