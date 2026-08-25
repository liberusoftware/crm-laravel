<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Liberu\CRM\ServiceAnalytics\Models\AnalyticsSnapshot;

final class MetricRecorded
{
    use Dispatchable;

    public function __construct(public readonly AnalyticsSnapshot $snapshot) {}
}
