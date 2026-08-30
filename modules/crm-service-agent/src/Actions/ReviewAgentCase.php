<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\ServiceAgent\Models\AgentCase;
use Liberu\CRM\ServiceAgent\Models\AgentReview;
use Liberu\CRM\ServiceAgent\Services\AgentAudit;
use Liberu\CRM\ServiceAgent\Services\AgentPolicy;

final class ReviewAgentCase
{
    public function execute(int $teamId, int $actorId, array $data): AgentReview
    {
        if (! app(AgentPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['case_id' => ['required', 'integer'], 'score' => ['required', 'integer', 'between:1,5'], 'feedback' => ['nullable', 'string'], 'status' => ['nullable', 'in:pending,approved,rejected']])->validate();
        if (! AgentCase::query()->where('team_id', $teamId)->whereKey($data['case_id'])->exists()) {
            throw ValidationException::withMessages(['case_id' => 'Case does not belong to this team.']);
        }$review = AgentReview::query()->updateOrCreate(['team_id' => $teamId, 'case_id' => $data['case_id'], 'reviewer_id' => $actorId], ['score' => $data['score'], 'feedback' => $data['feedback'] ?? null, 'status' => $data['status'] ?? 'pending']);
        app(AgentAudit::class)->record($teamId, $actorId, 'quality_reviewed', ['review_id' => $review->id]);

        return $review;
    }
}
