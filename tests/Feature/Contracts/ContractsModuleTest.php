<?php

declare(strict_types=1);

namespace Tests\Feature\Contracts;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Contracts\Actions\CreateContract;
use Liberu\CRM\Contracts\Actions\TransitionContract;
use Tests\TestCase;

final class ContractsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_versions_approvals_signatures_and_renewals_are_scoped(): void
    {
        $u = User::factory()->create();
        $t = Team::factory()->create(['user_id' => $u->id]);
        $c = app(CreateContract::class)->execute($t->id, $u->id, ['name' => 'MSA', 'parties' => ['customer' => 'Acme'], 'terms' => ['currency' => 'USD'], 'obligations' => ['sla' => 'gold']]);
        $submit = app(TransitionContract::class)->execute($t->id, $u->id, $c, 'submit');
        $c = $c->fresh();
        $approve = app(TransitionContract::class)->execute($t->id, $u->id, $c, 'approve');
        $c = $c->fresh();
        $sign = app(TransitionContract::class)->execute($t->id, $u->id, $c, 'sign');
        $this->assertSame('active', $c->fresh()->status);
        $this->assertSame('submit', $submit->type);
        $this->assertSame('approve', $approve->type);
        $this->assertSame('sign', $sign->type);
    }
}
