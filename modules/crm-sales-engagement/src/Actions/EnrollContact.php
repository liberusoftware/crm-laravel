<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesEngagement\Models\EngagementSequence;
use Liberu\CRM\SalesEngagement\Models\Enrollment;
use Liberu\CRM\SalesEngagement\Services\EngagementPolicy;

final class EnrollContact
{
    public function execute(int $teamId, int $actorId, array $data): Enrollment
    {
        if (! app(EngagementPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['sequence_id' => ['required', 'integer'], 'contact_id' => ['required', 'integer'], 'reentry' => ['nullable', 'boolean']])->validate();
        $sequence = EngagementSequence::query()->where('team_id', $teamId)->whereKey($data['sequence_id'])->firstOrFail();
        $enrollment = Enrollment::query()->where('team_id', $teamId)->where('sequence_id', $sequence->id)->where('contact_id', $data['contact_id'])->first();
        if ($enrollment !== null) {
            if (! ($data['reentry'] ?? false)) {
                return $enrollment;
            }$enrollment->reentry_count++;
            $enrollment->status = 'active';
            $enrollment->current_step = 0;
            $enrollment->save();

            return $enrollment;
        }

        return Enrollment::query()->create(['team_id' => $teamId, 'sequence_id' => $sequence->id, 'contact_id' => $data['contact_id'], 'status' => 'active', 'current_step' => 0, 'reentry_count' => 0]);
    }
}
