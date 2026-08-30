<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\LeadQualification\Models\QualifiedLead;
use Liberu\CRM\LeadQualification\Services\LeadQualificationPolicy;

final class UpsertLead
{
    public function __construct(private readonly LeadQualificationPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): QualifiedLead
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['external_key' => ['required', 'string', 'max:255'], 'stage' => ['nullable', 'in:new,working,nurture,qualified,converted,disqualified'], 'metadata' => ['nullable', 'array']])->validate();

        return QualifiedLead::query()->updateOrCreate(['team_id' => $teamId, 'external_key' => $data['external_key']], ['stage' => $data['stage'] ?? 'new', 'metadata' => $data['metadata'] ?? null]);
    }
}
