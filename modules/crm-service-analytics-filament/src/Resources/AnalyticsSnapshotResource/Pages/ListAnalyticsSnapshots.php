<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics\Filament\Resources\AnalyticsSnapshotResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ServiceAnalytics\Filament\Resources\AnalyticsSnapshotResource;

final class ListAnalyticsSnapshots extends ListRecords
{
    protected static string $resource = AnalyticsSnapshotResource::class;
}
