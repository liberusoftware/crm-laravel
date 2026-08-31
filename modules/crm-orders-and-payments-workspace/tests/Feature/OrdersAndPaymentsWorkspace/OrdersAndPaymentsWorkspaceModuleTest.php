<?php

declare(strict_types=1);

namespace Tests\Feature\OrdersAndPaymentsWorkspace;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Actions\CreateTransaction;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Actions\RecordPaymentEvent;
use Tests\TestCase;

final class OrdersAndPaymentsWorkspaceModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_lifecycle_is_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $tx = app(CreateTransaction::class)->execute($team->id, $owner->id, ['reference' => 'ORD-1', 'kind' => 'order', 'currency' => 'USD', 'amount' => 100]);
        app(RecordPaymentEvent::class)->execute($team->id, $owner->id, $tx, ['kind' => 'invoice_paid', 'status' => 'completed', 'amount' => 50]);
        $this->assertSame('50.00', (string) $tx->fresh()->paid_amount);
        $this->assertDatabaseMissing('crm_orders_payments', ['team_id' => $other->id, 'reference' => 'ORD-1']);
    }
}
