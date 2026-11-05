<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\App\Resources\LeadResource\Pages\ListLeads;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_search_and_score_filter_preserve_tenant_isolation(): void
    {
        $this->seed(RolesSeeder::class);
        $user = User::factory()->withPersonalTeam()->create(['email_verified_at' => now()]);
        $team = $user->currentTeam;
        setPermissionsTeamId($team->id);
        $user->assignRole('manager');
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($team);

        $contact = Contact::factory()->create(['team_id' => $team->id, 'name' => 'Alex Morgan']);
        $zero = Lead::factory()->create(['team_id' => $team->id, 'contact_id' => $contact->id, 'score' => 0]);
        $qualified = Lead::factory()->create(['team_id' => $team->id, 'score' => 80]);
        $otherTeam = User::factory()->withPersonalTeam()->create()->currentTeam;
        $foreign = Lead::factory()->create(['team_id' => $otherTeam->id, 'score' => 0]);

        Livewire::test(ListLeads::class)
            ->assertCanSeeTableRecords([$zero, $qualified])
            ->assertCanNotSeeTableRecords([$foreign])
            ->filterTable('score', ['score_from' => 0, 'score_to' => 0])
            ->assertCanSeeTableRecords([$zero])
            ->assertCanNotSeeTableRecords([$qualified, $foreign])
            ->searchTable('Alex Morgan')
            ->assertCanSeeTableRecords([$zero])
            ->assertCanNotSeeTableRecords([$foreign]);
    }
}
