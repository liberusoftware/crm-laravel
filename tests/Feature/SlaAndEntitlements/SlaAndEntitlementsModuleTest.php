<?php

declare(strict_types=1);

namespace Tests\Feature\SlaAndEntitlements;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\SlaAndEntitlements\Actions\CreateCalendar;
use Liberu\CRM\SlaAndEntitlements\Actions\CreateContract;
use Liberu\CRM\SlaAndEntitlements\Actions\EvaluateCase;
use Liberu\CRM\SlaAndEntitlements\Actions\OpenCase;
use Liberu\CRM\SlaAndEntitlements\Actions\RequestException;
use Liberu\CRM\SlaAndEntitlements\Actions\SetEntitlement;
use Liberu\CRM\SlaAndEntitlements\Actions\TransitionCase;
use Liberu\CRM\SlaAndEntitlements\Models\SlaCase;
use Liberu\CRM\SlaAndEntitlements\Models\SlaEscalation;
use Liberu\CRM\SlaAndEntitlements\Models\SlaException;
use Tests\TestCase;

final class SlaAndEntitlementsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_entitlement_case_and_exception_lifecycle_is_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $calendar = app(CreateCalendar::class)->execute($team->id, $owner->id, ['name' => 'Support hours', 'timezone' => 'UTC']);
        $contract = app(CreateContract::class)->execute($team->id, $owner->id, ['name' => 'Gold support', 'status' => 'active', 'calendar_id' => $calendar->id]);
        $entitlement = app(SetEntitlement::class)->execute($team->id, $owner->id, ['contract_id' => $contract->id, 'name' => 'Urgent', 'priority' => 'urgent', 'response_minutes' => 10, 'resolution_minutes' => 60, 'warning_minutes' => 5]);
        $case = app(OpenCase::class)->execute($team->id, $owner->id, ['subject' => 'Payment outage', 'contract_id' => $contract->id, 'entitlement_id' => $entitlement->id]);
        app(TransitionCase::class)->execute($team->id, $owner->id, $case->id, 'responded');
        app(RequestException::class)->execute($team->id, $owner->id, ['case_id' => $case->id, 'reason' => 'Customer maintenance window']);
        $evaluation = app(EvaluateCase::class)->execute($team->id, $owner->id, $case->id);

        self::assertFalse($evaluation['breached']);
        self::assertSame('responded', $case->fresh()->status);
        self::assertSame(1, SlaCase::query()->where('team_id', $team->id)->count());
        self::assertSame(1, SlaException::query()->where('team_id', $team->id)->count());
        self::assertSame(0, SlaEscalation::query()->where('team_id', $other->id)->count());
    }
}
