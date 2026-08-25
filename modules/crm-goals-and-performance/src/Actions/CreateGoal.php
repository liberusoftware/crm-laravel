<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformance\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\GoalsAndPerformance\Models\PerformanceGoal;
use Liberu\CRM\GoalsAndPerformance\Services\PerformancePolicy;

final class CreateGoal
{
    public function __construct(private readonly PerformancePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): PerformanceGoal
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['owner_id' => ['required', 'integer'], 'scope' => ['required', 'in:individual,team,company'], 'name' => ['required', 'string', 'max:255'], 'target' => ['required', 'numeric', 'min:0'], 'starts_on' => ['required', 'date'], 'ends_on' => ['nullable', 'date'], 'metadata' => ['nullable', 'array']])->validate();

        return PerformanceGoal::query()->create(['team_id' => $teamId, ...$data]);
    }
}
