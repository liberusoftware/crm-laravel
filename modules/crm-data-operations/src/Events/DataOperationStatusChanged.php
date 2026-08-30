<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\CRM\DataOperations\Models\DataOperation;

final class DataOperationStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly DataOperation $operation, public readonly string $previousStatus) {}
}
