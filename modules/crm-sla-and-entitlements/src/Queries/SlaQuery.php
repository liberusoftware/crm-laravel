<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Queries;

use Liberu\CRM\SlaAndEntitlements\Models\SlaCalendar;
use Liberu\CRM\SlaAndEntitlements\Models\SlaCase;
use Liberu\CRM\SlaAndEntitlements\Models\SlaContract;
use Liberu\CRM\SlaAndEntitlements\Models\SlaEntitlement;
use Liberu\CRM\SlaAndEntitlements\Models\SlaEscalation;
use Liberu\CRM\SlaAndEntitlements\Models\SlaException;

final class SlaQuery
{
    public function contracts(int $teamId)
    {
        return SlaContract::query()->where('team_id', $teamId)->latest();
    }

    public function calendars(int $teamId)
    {
        return SlaCalendar::query()->where('team_id', $teamId)->latest();
    }

    public function entitlements(int $teamId)
    {
        return SlaEntitlement::query()->where('team_id', $teamId)->latest();
    }

    public function cases(int $teamId)
    {
        return SlaCase::query()->where('team_id', $teamId)->latest();
    }

    public function escalations(int $teamId)
    {
        return SlaEscalation::query()->where('team_id', $teamId)->latest();
    }

    public function exceptions(int $teamId)
    {
        return SlaException::query()->where('team_id', $teamId)->latest();
    }
}
