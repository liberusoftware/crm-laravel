<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Filament\Resources\PersonalizationRuleResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\Personalization\Filament\Resources\PersonalizationRuleResource;

final class ListPersonalizationRules extends ListRecords
{
    protected static string $resource = PersonalizationRuleResource::class;
}
