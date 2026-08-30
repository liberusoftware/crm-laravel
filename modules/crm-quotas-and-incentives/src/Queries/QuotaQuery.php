<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Queries;

use Liberu\CRM\QuotasAndIncentives\Models\CommissionCredit;
use Liberu\CRM\QuotasAndIncentives\Models\CommissionDispute;
use Liberu\CRM\QuotasAndIncentives\Models\CommissionExport;
use Liberu\CRM\QuotasAndIncentives\Models\CommissionPlan;
use Liberu\CRM\QuotasAndIncentives\Models\Quota;

final class QuotaQuery
{
    public function quotas(int $teamId)
    {
        return Quota::query()->where('team_id', $teamId)->latest();
    }

    public function plans(int $teamId)
    {
        return CommissionPlan::query()->where('team_id', $teamId)->latest();
    }

    public function credits(int $teamId)
    {
        return CommissionCredit::query()->where('team_id', $teamId)->latest();
    }

    public function disputes(int $teamId)
    {
        return CommissionDispute::query()->where('team_id', $teamId)->latest();
    }

    public function exports(int $teamId)
    {
        return CommissionExport::query()->where('team_id', $teamId)->latest();
    }
}
