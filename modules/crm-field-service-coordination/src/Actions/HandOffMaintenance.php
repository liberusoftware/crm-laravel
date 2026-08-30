<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordination\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\FieldServiceCoordination\Models\MaintenanceHandoff;
use Liberu\CRM\FieldServiceCoordination\Models\ServiceAppointment;
use Liberu\CRM\FieldServiceCoordination\Services\FieldServicePolicy;

final class HandOffMaintenance
{
    public function __construct(private readonly FieldServicePolicy $policy) {}

    public function execute(int $teamId, int $userId, ServiceAppointment $appointment, array $input): MaintenanceHandoff
    {
        abort_unless($appointment->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['target' => ['nullable', 'string', 'max:80'], 'payload' => ['required', 'array']])->validate();

        return MaintenanceHandoff::query()->create(['team_id' => $teamId, 'appointment_id' => $appointment->id, 'actor_id' => $userId, 'status' => 'pending', 'handed_off_at' => now(), ...$data]);
    }
}
