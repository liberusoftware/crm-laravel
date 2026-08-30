<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Filament\Resources\ActivityResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\Activities\Filament\Resources\ActivityResource;

final class EditActivity extends EditRecord
{
    protected static string $resource = ActivityResource::class;
}
