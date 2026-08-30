<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\LeadQualification\Events\QualificationChanged;
use Liberu\CRM\LeadQualification\Models\LeadQualification;
use Liberu\CRM\LeadQualification\Services\QualificationAudit;
use Liberu\CRM\LeadQualification\Services\QualificationScorer;

final class CreateQualification
{
    private const STAGES = ['subscriber', 'lead', 'marketing_qualified', 'product_qualified', 'sales_qualified', 'service_qualified', 'opportunity', 'customer'];

    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, ?int $actorId, array $attributes): LeadQualification
    {
        $subjectType = trim((string) ($attributes['subject_type'] ?? ''));
        $subjectId = (int) ($attributes['subject_id'] ?? 0);
        $stage = (string) ($attributes['lifecycle_stage'] ?? 'subscriber');
        if ($subjectType === '' || $subjectId < 1) {
            throw ValidationException::withMessages(['subject' => 'A subject type and positive subject id are required.']);
        }
        if (! in_array($stage, self::STAGES, true)) {
            throw ValidationException::withMessages(['lifecycle_stage' => 'Unsupported lifecycle stage.']);
        }

        return DB::transaction(function () use ($teamId, $actorId, $attributes, $subjectType, $subjectId, $stage): LeadQualification {
            $existing = LeadQualification::query()->where('team_id', $teamId)->where('subject_type', $subjectType)->where('subject_id', $subjectId)->first();
            if ($existing !== null) {
                return $existing;
            }
            $scores = app(QualificationScorer::class)->scores($attributes);
            $qualification = LeadQualification::query()->create(array_merge($attributes, $scores, ['team_id' => $teamId, 'actor_id' => $actorId, 'subject_type' => $subjectType, 'subject_id' => $subjectId, 'lifecycle_stage' => $stage, 'qualification_status' => $attributes['qualification_status'] ?? 'unqualified', 'version' => 1]));
            $qualification->stageHistory()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'to_stage' => $stage, 'reason' => 'created']);
            app(QualificationAudit::class)->record($qualification, $actorId, 'qualification.created', ['stage' => $stage]);
            QualificationChanged::dispatch($qualification, 'created');

            return $qualification->refresh();
        });
    }
}
