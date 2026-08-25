<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Personalization\Models\PersonalizationDecision;
use Liberu\CRM\Personalization\Models\PersonalizationOutcome;
use Liberu\CRM\Personalization\Services\PersonalizationPolicy;

final class RecordPersonalizationOutcome
{
    public function __construct(private readonly PersonalizationPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): PersonalizationOutcome
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['decision_id' => ['required', 'integer'], 'event' => ['required', 'in:impression,click,conversion,unsubscribe'], 'payload' => ['nullable', 'array']])->validate();
        PersonalizationDecision::query()->where('team_id', $teamId)->findOrFail($data['decision_id']);

        return PersonalizationOutcome::query()->create(['team_id' => $teamId, ...$data, 'occurred_at' => now()]);
    }
}
