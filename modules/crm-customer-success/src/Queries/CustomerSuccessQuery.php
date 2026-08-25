<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccess\Queries;

use Liberu\CRM\CustomerSuccess\Models\SuccessCustomer;
use Liberu\CRM\CustomerSuccess\Models\SuccessRisk;

final class CustomerSuccessQuery
{
    public function customers(int $teamId)
    {
        return SuccessCustomer::query()->where('team_id', $teamId)->latest();
    }

    public function risks(int $teamId)
    {
        return SuccessRisk::query()->where('team_id', $teamId)->where('status', 'open')->latest();
    }
}
