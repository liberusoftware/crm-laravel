<?php

declare(strict_types=1);

namespace Tests\Feature\ResourcePlanning;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ResourcePlanning\Actions\CreateBooking;
use Liberu\CRM\ResourcePlanning\Actions\SetCapacity;
use Liberu\CRM\ResourcePlanning\Actions\UpsertSkill;
use Liberu\CRM\ResourcePlanning\Models\ResourceBooking;
use Liberu\CRM\ResourcePlanning\Models\ResourceCapacity;
use Liberu\CRM\ResourcePlanning\Models\ResourceSkill;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

final class ResourcePlanningModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_skills_capacity_and_booking_conflicts_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        app(UpsertSkill::class)->execute($team->id, $owner->id, ['name' => 'Laravel', 'proficiency' => 5]);
        app(SetCapacity::class)->execute($team->id, $owner->id, ['resource_id' => 77, 'period_start' => '2026-08-25', 'period_end' => '2026-08-31', 'available_hours' => 8]);
        $booking = app(CreateBooking::class)->execute($team->id, $owner->id, ['resource_id' => 77, 'subject_type' => 'project', 'subject_id' => 1, 'starts_at' => '2026-08-26 09:00', 'ends_at' => '2026-08-26 13:00', 'status' => 'confirmed']);

        self::assertSame(4.0, $booking->hours);
        self::assertSame(4.0, ResourceCapacity::query()->where('team_id', $team->id)->firstOrFail()->allocated_hours);
        self::assertCount(1, ResourceSkill::query()->where('team_id', $team->id)->get());
        self::assertCount(0, ResourceBooking::query()->where('team_id', $other->id)->get());
        $this->expectException(HttpException::class);
        app(CreateBooking::class)->execute($team->id, $owner->id, ['resource_id' => 77, 'subject_type' => 'project', 'subject_id' => 2, 'starts_at' => '2026-08-26 10:00', 'ends_at' => '2026-08-26 14:00', 'status' => 'tentative']);
    }
}
