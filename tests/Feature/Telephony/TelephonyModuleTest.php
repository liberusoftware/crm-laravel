<?php

declare(strict_types=1);

namespace Tests\Feature\Telephony;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\Telephony\Actions\ConfigureTelephony;
use Liberu\CRM\Telephony\Actions\CreateTelephonyQueue;
use Liberu\CRM\Telephony\Actions\LogTelephonyCall;
use Liberu\CRM\Telephony\Actions\UpdateCall;
use Liberu\CRM\Telephony\Actions\UpsertTelephonyNumber;
use Liberu\CRM\Telephony\Models\TelephonyCall;
use Liberu\CRM\Telephony\Models\TelephonyNumber;
use Liberu\CRM\Telephony\Models\TelephonyQueue;
use Tests\TestCase;

final class TelephonyModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_telephony_lifecycle_is_idempotent_and_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $otherTeam = Team::factory()->create(['user_id' => User::factory()->create()->id]);

        $number = app(UpsertTelephonyNumber::class)->execute($team->id, $owner->id, ['number' => '+15550100', 'provider' => 'twilio']);
        $queue = app(CreateTelephonyQueue::class)->execute($team->id, $owner->id, ['name' => 'Support']);
        $settings = app(ConfigureTelephony::class)->execute($team->id, $owner->id, ['provider' => 'twilio', 'ivr' => ['welcome' => 'Hello']]);
        $data = ['from_number' => '+15550101', 'to_number' => $number->number, 'idempotency_key' => 'call-1', 'number_id' => $number->id];
        $call = app(LogTelephonyCall::class)->execute($team->id, $owner->id, $data);
        $sameCall = app(LogTelephonyCall::class)->execute($team->id, $owner->id, $data);
        app(UpdateCall::class)->execute($team->id, $owner->id, $call->id, ['disposition' => 'resolved', 'transfer_to' => '+15550102']);

        self::assertSame($call->id, $sameCall->id);
        self::assertSame(1, TelephonyCall::query()->where('team_id', $team->id)->count());
        self::assertSame(1, TelephonyNumber::query()->where('team_id', $team->id)->count());
        self::assertSame(1, TelephonyQueue::query()->where('team_id', $team->id)->count());
        self::assertSame(1, $settings->version);
        self::assertSame(0, TelephonyCall::query()->where('team_id', $otherTeam->id)->count());
        self::assertSame('resolved', $call->fresh()->disposition);
        self::assertSame('Support', $queue->name);
    }

    public function test_non_manager_cannot_mutate_telephony(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $team->users()->attach($member, ['role' => 'sales rep']);

        $this->expectException(ValidationException::class);
        app(CreateTelephonyQueue::class)->execute($team->id, $member->id, ['name' => 'Restricted']);
    }
}
