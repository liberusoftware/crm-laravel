<?php

declare(strict_types=1);

namespace Tests\Feature\Cpq;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\CPQ\Actions\PriceQuote;
use Liberu\CRM\CPQ\Actions\SubmitQuote;
use Tests\TestCase;

final class CpqModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_quote_pricing_and_approval_are_tenant_scoped(): void
    {
        $u = User::factory()->create();
        $t = Team::factory()->create(['user_id' => $u->id]);
        $q = app(PriceQuote::class)->execute($t->id, $u->id, ['name' => 'Proposal', 'lines' => [['unit_price' => 100, 'quantity' => 2, 'discount' => 10]]]);
        $a = app(SubmitQuote::class)->execute($t->id, $u->id, $q);
        $this->assertSame(190.0, $q->total);
        $this->assertSame('pending_approval', $q->fresh()->status);
        $this->assertSame('pending', $a->status);
    }
}
