<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\RevenueIntelligence\Models\RevenueInsight;
use Liberu\CRM\RevenueIntelligence\Services\RevenueIntelligencePolicy;

final class RecordInsight
{
    public function __construct(private readonly RevenueIntelligencePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): RevenueInsight
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['subject_type' => ['required', 'string', 'max:255'], 'subject_id' => ['required', 'integer'], 'kind' => ['required', 'in:pipeline,health,engagement,score,relationship,whitespace,anomaly'], 'score' => ['nullable', 'integer', 'between:0,100'], 'severity' => ['required', 'in:info,warning,critical'], 'payload' => ['nullable', 'array']])->validate();

        return RevenueInsight::query()->create(['team_id' => $teamId, ...$data, 'observed_at' => now()]);
    }
}
