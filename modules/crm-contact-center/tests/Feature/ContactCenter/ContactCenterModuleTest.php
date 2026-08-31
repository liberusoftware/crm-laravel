<?php

declare(strict_types=1);

namespace Tests\Feature\ContactCenter;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ContactCenter\Actions\RouteInteraction;
use Liberu\CRM\ContactCenter\Actions\SetAgentPresence;
use Liberu\CRM\ContactCenter\Queries\ContactCenterQuery;
use Tests\TestCase;

final class ContactCenterModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_presence_capacity_routing_and_supervisor_view_are_scoped(): void
    {
        $u = User::factory()->create();
        $t = Team::factory()->create(['user_id' => $u->id]);
        app(SetAgentPresence::class)->execute($t->id, $u->id, 'available', 2, ['billing']);
        $event = app(RouteInteraction::class)->execute($t->id, 'priority', 'billing', 120);
        $view = app(ContactCenterQuery::class)->supervisorView($t->id);
        $this->assertSame('routed', $event->type);
        $this->assertSame(1, $view['open_events']);
        $this->assertSame(1, $view['agents']->count());
    }
}
