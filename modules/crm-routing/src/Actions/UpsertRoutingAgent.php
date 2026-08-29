<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\Routing\Models\RoutingAgent;
use Liberu\CRM\Routing\Services\RoutingPolicy;

final class UpsertRoutingAgent
{
    public function execute(int $teamId, int $actorId, array $data): RoutingAgent
    {
        if (! app(RoutingPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['user_id' => ['required', 'integer'], 'territories' => ['nullable', 'array'], 'skills' => ['nullable', 'array'], 'languages' => ['nullable', 'array'], 'availability' => ['nullable', 'array'], 'workload' => ['nullable', 'integer', 'min:0'], 'value_capacity' => ['nullable', 'numeric', 'min:0'], 'sla_minutes' => ['nullable', 'integer', 'min:1']])->validate();

        return RoutingAgent::query()->updateOrCreate(['team_id' => $teamId, 'user_id' => $data['user_id']], array_merge($data, ['active' => true]));
    }
}
