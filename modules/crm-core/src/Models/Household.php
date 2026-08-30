<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Models;

use Liberu\CRM\Core\Enums\RecordType;

final class Household extends Record
{
    protected static function booted(): void
    {
        self::creating(function (self $record): void {
            $record->record_type = RecordType::Household->value;
        });
    }
}
