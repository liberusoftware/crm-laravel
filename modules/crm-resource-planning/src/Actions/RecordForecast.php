<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ResourcePlanning\Models\ResourceForecast;
use Liberu\CRM\ResourcePlanning\Services\ResourcePlanningPolicy;

final class RecordForecast
{
    public function __construct(private readonly ResourcePlanningPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ResourceForecast
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['period_start' => ['required', 'date'], 'period_end' => ['required', 'date', 'after_or_equal:period_start'], 'demand_hours' => ['required', 'numeric', 'min:0'], 'available_hours' => ['required', 'numeric', 'min:0'], 'assumptions' => ['nullable', 'array']])->validate();

        return ResourceForecast::query()->create(['team_id' => $teamId, ...$data]);
    }
}
