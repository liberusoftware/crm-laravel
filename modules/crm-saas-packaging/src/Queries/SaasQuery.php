<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Queries;

use Liberu\CRM\SaasPackaging\Models\SaasPlan;
use Liberu\CRM\SaasPackaging\Models\SaasSubscription;
use Liberu\CRM\SaasPackaging\Models\SaasUsage;

final class SaasQuery
{
    public function plans()
    {
        return SaasPlan::query()->where('active', true)->orderBy('price');
    }

    public function subscription(int $teamId): ?SaasSubscription
    {
        return SaasSubscription::query()->where('team_id', $teamId)->first();
    }

    public function usage(int $teamId)
    {
        return SaasUsage::query()->where('team_id', $teamId)->latest('period_start');
    }
}
