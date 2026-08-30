<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Liberu\CRM\Activities\Models\Activity;

final class ActivityScheduler
{
    public function nextOccurrence(Activity $activity): ?Activity
    {
        if ($activity->recurrence === null || $activity->due_at === null) {
            return null;
        }
        $due = match ($activity->recurrence) {
            'daily' => $activity->due_at->copy()->addDay(),
            'weekly' => $activity->due_at->copy()->addWeek(),
            'monthly' => $activity->due_at->copy()->addMonth(),
            default => $activity->due_at->copy(),
        };
        if ($activity->recurrence_until?->isBefore($due)) {
            return null;
        }
        $attributes = $activity->only(['assigned_to', 'kind', 'title', 'description', 'subject_type', 'subject_id', 'queue', 'metadata']);
        $attributes['due_at'] = $due;
        $attributes['starts_at'] = $activity->starts_at?->copy()->addSeconds($due->diffInSeconds($activity->due_at));
        $attributes['recurrence'] = $activity->recurrence;
        $attributes['recurrence_until'] = $activity->recurrence_until;
        $attributes['reminder_at'] = $activity->reminder_at?->copy()->addSeconds($due->diffInSeconds($activity->due_at));

        return Activity::query()->create(array_merge($attributes, ['team_id' => $activity->team_id, 'actor_id' => $activity->actor_id, 'status' => 'planned']));
    }

    /** @return Collection<int, Activity> */
    public function dueReminders(int $teamId, ?Carbon $at = null): Collection
    {
        $at ??= now();

        return Activity::query()->where('team_id', $teamId)->where('status', 'planned')->whereNotNull('reminder_at')->where('reminder_at', '<=', $at)->where(fn ($query) => $query->whereNull('completed_at'))->get();
    }
}
