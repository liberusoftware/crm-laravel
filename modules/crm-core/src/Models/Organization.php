<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Models;

use Liberu\CRM\Core\Enums\RecordType;

final class Organization extends Record
{
    protected static function booted(): void
    {
        self::creating(function (self $record): void {
            $record->record_type = RecordType::Organization->value;
        });
    }
}
