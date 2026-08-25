<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordination\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\FieldServiceCoordination\Models\ServiceAppointment;
use Liberu\CRM\FieldServiceCoordination\Models\ServiceHistory;
use Liberu\CRM\FieldServiceCoordination\Services\FieldServicePolicy;

final class RecordServiceHistory
{
    public function __construct(private readonly FieldServicePolicy $policy) {}

    public function execute(int $teamId, int $userId, ServiceAppointment $appointment, array $input): ServiceHistory
    {
        abort_unless($appointment->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['asset_id' => ['nullable', 'integer', 'exists:crm_field_service_assets,id'], 'event' => ['required', 'string', 'max:80'], 'details' => ['nullable', 'string']])->validate();

        return ServiceHistory::query()->create(['team_id' => $teamId, 'appointment_id' => $appointment->id, 'actor_id' => $userId, ...$data]);
    }
}
