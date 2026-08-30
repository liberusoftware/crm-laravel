<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Filament\Resources\WebIntentVisitResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\WebIntent\Filament\Resources\WebIntentVisitResource;

final class ListWebIntentVisits extends ListRecords
{
    protected static string $resource = WebIntentVisitResource::class;
}
