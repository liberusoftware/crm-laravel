<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Filament\Resources\InsightResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\RevenueIntelligence\Filament\Resources\InsightResource;

final class ListInsights extends ListRecords
{
    protected static string $resource = InsightResource::class;
}
