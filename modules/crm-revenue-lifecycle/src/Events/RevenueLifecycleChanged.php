<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Events;

use Liberu\CRM\RevenueLifecycle\Models\RevenueAsset;

final readonly class RevenueLifecycleChanged
{
    public function __construct(public RevenueAsset $asset, public string $action) {}
}
