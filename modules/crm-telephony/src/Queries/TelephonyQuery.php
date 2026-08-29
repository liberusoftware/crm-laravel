<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Queries;

use Liberu\CRM\Telephony\Models\TelephonyCall;
use Liberu\CRM\Telephony\Models\TelephonyNumber;
use Liberu\CRM\Telephony\Models\TelephonyQueue;
use Liberu\CRM\Telephony\Models\TelephonySettings;

final class TelephonyQuery
{
    public function numbers(int $teamId)
    {
        return TelephonyNumber::query()->where('team_id', $teamId)->latest();
    }

    public function queues(int $teamId)
    {
        return TelephonyQueue::query()->where('team_id', $teamId)->latest();
    }

    public function calls(int $teamId)
    {
        return TelephonyCall::query()->where('team_id', $teamId)->latest();
    }

    public function settings(int $teamId): ?TelephonySettings
    {
        return TelephonySettings::query()->where('team_id', $teamId)->first();
    }
}
