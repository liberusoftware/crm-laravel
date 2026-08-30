<?php

declare(strict_types=1);

namespace Tests\Feature\Activities;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\Activities\Actions\CompleteActivities;
use Liberu\CRM\Activities\Actions\CreateActivity;
use Liberu\CRM\Activities\Models\Activity;
use Liberu\CRM\Activities\Services\ActivityReport;
use Liberu\CRM\Activities\Services\ActivityScheduler;
use Tests\TestCase;

final class ActivitiesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_lifecycle_recurrence_and_reporting_are_team_scoped(): void
    {
        $activity = app(CreateActivity::class)->execute(7, 11, ['kind' => 'task', 'title' => 'Follow up', 'due_at' => now()->addDay(), 'recurrence' => 'weekly', 'reminder_at' => now()->subMinute()]);
        self::assertSame('planned', $activity->status);
        $next = app(ActivityScheduler::class)->nextOccurrence($activity);
        self::assertNotNull($next);
        self::assertSame(7, $next->team_id);
        self::assertCount(2, app(ActivityScheduler::class)->dueReminders(7));
        self::assertCount(0, app(ActivityScheduler::class)->dueReminders(8));
        self::assertSame(0.0, app(ActivityReport::class)->summarize(7, now()->subDay(), now()->addDay())['completion_rate']);
    }

    public function test_bulk_completion_does_not_cross_team_boundary(): void
    {
        $first = app(CreateActivity::class)->execute(7, null, ['kind' => 'call', 'title' => 'Call']);
        $other = app(CreateActivity::class)->execute(8, null, ['kind' => 'call', 'title' => 'Other']);
        self::assertSame(1, app(CompleteActivities::class)->execute(7, [$first->getKey(), $other->getKey()], 'connected'));
        self::assertSame('completed', Activity::query()->findOrFail($first->getKey())->status);
        self::assertSame('planned', Activity::query()->findOrFail($other->getKey())->status);
    }

    public function test_meetings_require_a_start_time(): void
    {
        $this->expectException(ValidationException::class);
        app(CreateActivity::class)->execute(7, null, ['kind' => 'meeting', 'title' => 'Demo']);
    }
}
