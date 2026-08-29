<?php

declare(strict_types=1);

namespace Tests\Feature\FieldServiceCoordination;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\FieldServiceCoordination\Actions\CreateWorkType;
use Liberu\CRM\FieldServiceCoordination\Actions\HandOffMaintenance;
use Liberu\CRM\FieldServiceCoordination\Actions\RecordServiceHistory;
use Liberu\CRM\FieldServiceCoordination\Actions\ScheduleAppointment;
use Tests\TestCase;

final class FieldServiceCoordinationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_appointment_history_and_maintenance_handoff_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $type = app(CreateWorkType::class)->execute($team->id, $owner->id, ['name' => 'Install', 'code' => 'install']);
        $appointment = app(ScheduleAppointment::class)->execute($team->id, $owner->id, ['work_type_id' => $type->id, 'subject' => 'Install unit', 'starts_at' => '2026-09-01 09:00', 'ends_at' => '2026-09-01 10:00']);
        $history = app(RecordServiceHistory::class)->execute($team->id, $owner->id, $appointment, ['event' => 'completed', 'details' => 'Installed']);
        $handoff = app(HandOffMaintenance::class)->execute($team->id, $owner->id, $appointment, ['payload' => ['reason' => 'preventive']]);
        $this->assertSame($team->id, $history->team_id);
        $this->assertSame($team->id, $handoff->team_id);
        $this->assertDatabaseHas('crm_field_service_handoffs', ['appointment_id' => $appointment->id, 'target' => 'maintenance']);
    }
}
