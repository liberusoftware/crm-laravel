<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\CRM\Activities\Models\Activity;

final class ActivityCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Activity $activity) {}
}
