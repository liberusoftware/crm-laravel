<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\ServiceAgent\Events\AgentCaseUpdated;
use Liberu\CRM\ServiceAgent\Models\AgentCase;
use Liberu\CRM\ServiceAgent\Services\AgentAudit;
use Liberu\CRM\ServiceAgent\Services\AgentPolicy;

final class ClassifyCase
{
    public function execute(int $teamId, int $actorId, int $caseId, array $data): AgentCase
    {
        if (! app(AgentPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['classification' => ['required', 'string', 'max:100'], 'confidence' => ['required', 'numeric', 'between:0,1']])->validate();
        $case = AgentCase::query()->where('team_id', $teamId)->findOrFail($caseId);
        $case->fill(['classification' => $data['classification'], 'confidence' => $data['confidence'], 'status' => 'classified'])->save();
        app(AgentAudit::class)->record($teamId, $actorId, 'case_classified', ['case_id' => $case->id, 'confidence' => $case->confidence]);
        AgentCaseUpdated::dispatch($case, 'classified');

        return $case;
    }
}
