<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\Routing\Models\RoutingAgent;
use Liberu\CRM\Routing\Models\RoutingAssignment;
use Liberu\CRM\Routing\Services\RoutingPolicy;

final class AssignSubject
{
    public function execute(int $teamId, int $actorId, array $data): RoutingAssignment
    {
        if (! app(RoutingPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['subject_type' => ['required', 'string', 'max:100'], 'subject_id' => ['required', 'integer'], 'skills' => ['nullable', 'array'], 'language' => ['nullable', 'string'], 'territory' => ['nullable', 'string'], 'acceptance_minutes' => ['nullable', 'integer', 'min:1']])->validate();

        return DB::transaction(function () use ($teamId, $data) {
            $agents = RoutingAgent::query()->where('team_id', $teamId)->where('active', true)->orderBy('workload')->orderBy('last_assigned_at')->lockForUpdate()->get();
            $agent = null;
            foreach ($agents as $candidate) {
                if ($this->matches($candidate, $data)) {
                    $agent = $candidate;
                    break;
                }
            }
            if ($agent === null) {
                $agent = $agents->first();
            }if ($agent === null) {
                throw ValidationException::withMessages(['assignment' => 'No available routing agent.']);
            }$agent->increment('workload');
            $agent->update(['last_assigned_at' => now()]);

            return RoutingAssignment::query()->create(['team_id' => $teamId, 'agent_id' => $agent->id, 'subject_type' => $data['subject_type'], 'subject_id' => $data['subject_id'], 'status' => 'pending', 'acceptance_due_at' => now()->addMinutes($data['acceptance_minutes'] ?? 15), 'fallback_at' => now()->addMinutes(($data['acceptance_minutes'] ?? 15) + 5), 'criteria' => $data]);
        });
    }

    private function matches(RoutingAgent $agent, array $data): bool
    {
        $skills = $data['skills'] ?? [];
        $agentSkills = $agent->skills ?? [];

        return (($data['language'] ?? null) === null || in_array($data['language'], $agent->languages ?? [], true))
            && count(array_diff($skills, $agentSkills)) === 0;
    }
}
