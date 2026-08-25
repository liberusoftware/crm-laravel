<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\ServiceAgent\Events\AgentCaseUpdated;
use Liberu\CRM\ServiceAgent\Models\AgentCase;
use Liberu\CRM\ServiceAgent\Services\AgentAudit;
use Liberu\CRM\ServiceAgent\Services\AgentPolicy;

final class CreateAgentCase
{
    public function execute(int $teamId, int $actorId, array $data): AgentCase
    {
        if (! app(AgentPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['subject' => ['required', 'string', 'max:255'], 'input' => ['required', 'string'], 'idempotency_key' => ['required', 'string', 'max:255']])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $data) {
            $existing = AgentCase::query()->where('team_id', $teamId)->where('idempotency_key', $data['idempotency_key'])->lockForUpdate()->first();
            if ($existing) {
                return $existing;
            }$case = AgentCase::query()->create(['team_id' => $teamId, 'subject' => $data['subject'], 'input' => $data['input'], 'idempotency_key' => $data['idempotency_key'], 'metadata' => $data['metadata'] ?? []]);
            app(AgentAudit::class)->record($teamId, $actorId, 'case_created', ['case_id' => $case->id]);
            AgentCaseUpdated::dispatch($case, 'created');

            return $case;
        });
    }
}
