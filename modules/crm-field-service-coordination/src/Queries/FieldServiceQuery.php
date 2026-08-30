<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordination\Queries;

use Liberu\CRM\FieldServiceCoordination\Models\ServiceAppointment;
use Liberu\CRM\FieldServiceCoordination\Models\ServiceAsset;
use Liberu\CRM\FieldServiceCoordination\Models\WorkType;

final class FieldServiceQuery
{
    public function workTypes(int $teamId)
    {
        return WorkType::query()->where('team_id', $teamId)->where('active', true)->orderBy('name');
    }

    public function appointments(int $teamId)
    {
        return ServiceAppointment::query()->where('team_id', $teamId)->orderBy('starts_at');
    }

    public function assets(int $teamId)
    {
        return ServiceAsset::query()->where('team_id', $teamId)->orderBy('name');
    }
}
