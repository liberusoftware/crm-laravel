<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordination\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\FieldServiceCoordination\Models\ServiceAppointment;
use Liberu\CRM\FieldServiceCoordination\Services\FieldServicePolicy;

final class ScheduleAppointment
{
    public function __construct(private readonly FieldServicePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ServiceAppointment
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['work_type_id' => ['required', 'integer', 'exists:crm_field_service_work_types,id'], 'technician_id' => ['nullable', 'integer'], 'subject' => ['required', 'string', 'max:160'], 'location' => ['nullable', 'string', 'max:255'], 'starts_at' => ['required', 'date'], 'ends_at' => ['required', 'date', 'after:starts_at'], 'notes' => ['nullable', 'string'], 'dispatch' => ['nullable', 'array']])->validate();

        return ServiceAppointment::query()->create(['team_id' => $teamId, ...$data]);
    }
}
