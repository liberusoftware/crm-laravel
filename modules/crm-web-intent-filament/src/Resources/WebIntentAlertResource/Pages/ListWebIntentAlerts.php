<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Filament\Resources\WebIntentAlertResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\WebIntent\Filament\Resources\WebIntentAlertResource;

final class ListWebIntentAlerts extends ListRecords
{
    protected static string $resource = WebIntentAlertResource::class;
}
