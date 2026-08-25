<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistration\Queries;

use Liberu\CRM\DealRegistration\Models\DealRegistration;

final class DealRegistrationQuery
{
    public function deals(int $teamId)
    {
        return DealRegistration::query()->where('team_id', $teamId)->latest();
    }

    public function protected(int $teamId)
    {
        return DealRegistration::query()->where('team_id', $teamId)->where('status', 'protected')->where('protection_until', '>', now())->latest();
    }
}
