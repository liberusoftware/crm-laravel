<?php

declare(strict_types=1);

namespace Tests\Feature\ProductWorkspace;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ProductWorkspace\Actions\GrantEntitlement;
use Liberu\CRM\ProductWorkspace\Actions\RecordProductSync;
use Liberu\CRM\ProductWorkspace\Actions\UpsertWorkspaceProduct;
use Tests\TestCase;

final class ProductWorkspaceModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_entitlement_and_governed_sync_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $product = app(UpsertWorkspaceProduct::class)->execute($team->id, $owner->id, ['sku' => 'PRO-1', 'name' => 'Professional', 'price' => 99.5, 'price_book' => ['annual' => 999]]);
        $entitlement = app(GrantEntitlement::class)->execute($team->id, $owner->id, ['customer_id' => $owner->id, 'product_id' => $product->id, 'starts_at' => '2026-01-01']);
        $sync = app(RecordProductSync::class)->execute($team->id, $owner->id, ['provider' => 'billing', 'resource' => 'products', 'status' => 'completed']);
        $this->assertSame($team->id, $entitlement->team_id);
        $this->assertSame('completed', $sync->status);
        $this->assertDatabaseHas('crm_product_workspace_products', ['team_id' => $team->id, 'sku' => 'PRO-1']);
    }
}
