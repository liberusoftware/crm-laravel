<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\Routing\Models\RoutingRule;
use Liberu\CRM\Routing\Services\RoutingPolicy;

final class CreateRoutingRule
{
    public function execute(int $teamId, int $actorId, array $data): RoutingRule
    {
        if (! app(RoutingPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['name' => ['required', 'string', 'max:255'], 'priority' => ['nullable', 'integer', 'min:0'], 'conditions' => ['nullable', 'array'], 'action' => ['nullable', 'array']])->validate();

        return RoutingRule::query()->create(array_merge($data, ['team_id' => $teamId, 'active' => true]));
    }
}
