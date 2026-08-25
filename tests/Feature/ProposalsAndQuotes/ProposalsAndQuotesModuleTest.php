<?php

declare(strict_types=1);

namespace Tests\Feature\ProposalsAndQuotes;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ProposalsAndQuotes\Actions\ChangeProposalStatus;
use Liberu\CRM\ProposalsAndQuotes\Actions\CreateProposal;
use Liberu\CRM\ProposalsAndQuotes\Actions\CreateProposalVersion;
use Liberu\CRM\ProposalsAndQuotes\Models\Proposal;
use Liberu\CRM\ProposalsAndQuotes\Models\ProposalVersion;
use Tests\TestCase;

final class ProposalsAndQuotesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_versioned_quote_acceptance_and_expiry_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $proposal = app(CreateProposal::class)->execute($team->id, $owner->id, ['title' => 'Acme Quote', 'currency' => 'USD', 'expires_at' => '2026-09-30']);
        $version = app(CreateProposalVersion::class)->execute($team->id, $owner->id, ['proposal_id' => $proposal->id, 'scope' => ['support' => true], 'line_items' => [['description' => 'Implementation', 'quantity' => 2, 'unit_price' => 500]]]);
        app(ChangeProposalStatus::class)->execute($team->id, $owner->id, $proposal->id, 'delivered');
        app(ChangeProposalStatus::class)->execute($team->id, $owner->id, $proposal->id, 'accepted');

        self::assertSame(1000.0, $proposal->refresh()->total);
        self::assertSame(1, $version->version);
        self::assertSame('accepted', $proposal->status);
        self::assertCount(1, ProposalVersion::query()->where('team_id', $team->id)->get());
        self::assertCount(0, Proposal::query()->where('team_id', $other->id)->get());
    }
}
