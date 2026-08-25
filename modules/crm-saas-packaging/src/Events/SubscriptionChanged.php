<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Liberu\CRM\SaasPackaging\Models\SaasSubscription;

final class SubscriptionChanged
{
    use Dispatchable;

    public function __construct(public readonly SaasSubscription $subscription, public readonly string $operation) {}
}
