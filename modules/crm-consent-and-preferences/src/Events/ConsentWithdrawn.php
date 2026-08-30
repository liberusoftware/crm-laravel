<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\CRM\ConsentAndPreferences\Models\ConsentRecord;

final class ConsentWithdrawn
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly ConsentRecord $consent) {}
}
