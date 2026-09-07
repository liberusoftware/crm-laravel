<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesEngagement\Models\EngagementSequence;
use Liberu\CRM\SalesEngagement\Models\EngagementStep;
use Liberu\CRM\SalesEngagement\Models\EngagementTask;
use Liberu\CRM\SalesEngagement\Models\Enrollment;
use Liberu\CRM\SalesEngagement\Services\EngagementPolicy;

final class EnrollContact
{
    public function execute(int $teamId, int $actorId, array $data): Enrollment
    {
        if (! app(EngagementPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        validator($data, ['sequence_id' => ['required', 'integer'], 'contact_id' => ['required', 'integer'], 'reentry' => ['nullable', 'boolean']])->validate();

        return DB::transaction(function () use ($teamId, $data): Enrollment {
            $sequence = EngagementSequence::query()->where('team_id', $teamId)->lockForUpdate()->findOrFail($data['sequence_id']);
            if (! DB::table('contacts')->where('team_id', $teamId)->where('id', $data['contact_id'])->exists()) {
                throw ValidationException::withMessages(['contact_id' => 'Contact does not belong to this team.']);
            }

            $enrollment = Enrollment::query()->where('team_id', $teamId)->where('sequence_id', $sequence->id)
                ->where('contact_id', $data['contact_id'])->lockForUpdate()->first();
            if ($enrollment !== null && ! ($data['reentry'] ?? false)) {
                return $enrollment;
            }

            $firstStep = EngagementStep::query()->where('sequence_id', $sequence->id)->orderBy('position')->first();
            $attributes = [
                'status' => 'active',
                'current_step' => 0,
                'next_run_at' => now()->addMinutes($firstStep?->delay_minutes ?? 0),
            ];

            if ($enrollment !== null) {
                EngagementTask::query()->where('team_id', $teamId)->where('enrollment_id', $enrollment->id)
                    ->where('status', 'queued')->update(['status' => 'cancelled']);
                $enrollment->update([...$attributes, 'reentry_count' => $enrollment->reentry_count + 1]);

                return $enrollment;
            }

            return Enrollment::query()->create([
                ...$attributes,
                'team_id' => $teamId,
                'sequence_id' => $sequence->id,
                'contact_id' => $data['contact_id'],
                'reentry_count' => 0,
            ]);
        });
    }
}
