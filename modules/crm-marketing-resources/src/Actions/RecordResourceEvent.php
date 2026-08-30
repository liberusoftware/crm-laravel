<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingResources\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\MarketingResources\Models\MarketingResource;
use Liberu\CRM\MarketingResources\Models\MarketingResourceEvent;
use Liberu\CRM\MarketingResources\Services\MarketingResourcePolicy;

final class RecordResourceEvent
{
    public function __construct(private readonly MarketingResourcePolicy $policy) {}

    public function execute(int $teamId, int $userId, MarketingResource $resource, array $input): MarketingResourceEvent
    {
        abort_unless($resource->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:approval,usage_right,publication,archive'], 'status' => ['required', 'in:pending,approved,rejected,recorded'], 'notes' => ['nullable', 'string'], 'expires_at' => ['nullable', 'date']])->validate();
        $event = MarketingResourceEvent::query()->create(['team_id' => $teamId, 'resource_id' => $resource->id, 'actor_id' => $userId, ...$data]);
        if ($event->kind === 'approval' && $event->status === 'approved') {
            $resource->update(['status' => 'approved']);
        }

        return $event;
    }
}
