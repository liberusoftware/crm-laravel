<?php

declare(strict_types=1);

namespace Tests\Feature\DealRegistration;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\DealRegistration\Actions\ApproveDeal;
use Liberu\CRM\DealRegistration\Actions\CollaborateOnDeal;
use Liberu\CRM\DealRegistration\Actions\SubmitDeal;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

final class DealRegistrationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_protection_approval_and_collaboration_are_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $deal = app(SubmitDeal::class)->execute($team->id, $owner->id, ['external_key' => 'partner-1', 'company' => 'Acme', 'contact_email' => 'buyer@acme.test', 'territory' => 'NA', 'attribution' => ['partner' => 'reseller']]);
        $approved = app(ApproveDeal::class)->execute($team->id, $owner->id, $deal, 30);
        $collaborated = app(CollaborateOnDeal::class)->execute($team->id, $owner->id, $approved, ['collaborator_id' => $owner->id, 'role' => 'co-sell']);
        $this->assertSame('protected', $collaborated->status);
        $this->assertNotNull($collaborated->protection_until);
        $this->assertCount(1, $collaborated->collaborators);
        $this->expectException(HttpException::class);
        app(SubmitDeal::class)->execute($team->id, $owner->id, ['external_key' => 'partner-2', 'company' => 'Acme', 'contact_email' => 'buyer@acme.test']);
    }
}
