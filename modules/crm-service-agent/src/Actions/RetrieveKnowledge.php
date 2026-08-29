<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\ServiceAgent\Models\AgentKnowledge;
use Liberu\CRM\ServiceAgent\Services\AgentPolicy;

final class RetrieveKnowledge
{
    public function execute(int $teamId, int $actorId, array $data)
    {
        if (! app(AgentPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['search' => ['required', 'string', 'max:255'], 'limit' => ['nullable', 'integer', 'min:1', 'max:50']])->validate();
        $search = $data['search'];

        return AgentKnowledge::query()->where('team_id', $teamId)->where('active', true)->where(fn ($q) => $q->where('title', 'like', '%'.$search.'%')->orWhere('content', 'like', '%'.$search.'%'))->limit($data['limit'] ?? 10)->get();
    }
}
