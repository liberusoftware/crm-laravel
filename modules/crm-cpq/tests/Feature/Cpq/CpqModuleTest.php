<?php

declare(strict_types=1);

namespace Tests\Feature\Cpq;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\CRM\CPQ\Actions\PriceQuote;
use Liberu\CRM\CPQ\Actions\SubmitQuote;
use Liberu\CRM\CPQApi\CpqApiServiceProvider;
use Liberu\CRM\CPQLivewire\Components\QuoteBuilder;
use Livewire\Livewire;
use Tests\TestCase;

final class CpqModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->register(CpqApiServiceProvider::class);
        (new CpqApiServiceProvider($this->app))->boot();
        $this->app['view']->addNamespace('crm-cpq-livewire', __DIR__.'/../../../../crm-cpq-livewire/resources/views');
    }

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

    public function test_quote_pricing_rejects_empty_or_invalid_lines(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        app(PriceQuote::class)->execute(1, 1, ['lines' => [['unit_price' => 10, 'quantity' => 0]]]);
    }

    public function test_quote_api_hides_foreign_quotes(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        Sanctum::actingAs($user);
        $foreign = app(PriceQuote::class)->execute(999, 1, ['lines' => [['unit_price' => 10, 'quantity' => 1]]]);

        $this->getJson("/api/v1/crm/cpq/quotes/{$foreign->id}")->assertNotFound();
    }

    public function test_quote_builder_adds_and_removes_lines(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        Sanctum::actingAs($user);

        $component = Livewire::test(QuoteBuilder::class)
            ->call('addLine')
            ->assertSet('lines', [
                ['description' => '', 'unit_price' => 0, 'quantity' => 1, 'discount' => 0],
                ['description' => '', 'unit_price' => 0, 'quantity' => 1, 'discount' => 0],
            ]);

        $component->call('removeLine', 1)->assertCount('lines', 1);
    }
}
