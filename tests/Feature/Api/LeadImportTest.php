<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LeadImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_creates_a_contact_and_lead_and_deduplicates_repeated_rows(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        Sanctum::actingAs($user);
        $csv = "name,email,phone,company,source,external_key\nJane Smith,jane@example.com,+441234567890,Example Ltd,website,crm-001\n";

        $this->postJson('/api/v1/leads/import', [
            'file' => UploadedFile::fake()->createWithContent('leads.csv', $csv),
        ])->assertOk()->assertJsonPath('created', 1);

        $this->postJson('/api/v1/leads/import', [
            'file' => UploadedFile::fake()->createWithContent('leads.csv', $csv),
        ])->assertOk()->assertJsonPath('updated', 1);

        $this->assertSame(1, Contact::withoutGlobalScopes()->where('team_id', $user->currentTeam->id)->count());
        $this->assertSame(1, Lead::withoutGlobalScopes()->where('team_id', $user->currentTeam->id)->count());
        $this->assertDatabaseHas('leads', ['team_id' => $user->currentTeam->id, 'import_key' => 'crm-001']);
    }

    public function test_import_rejects_a_file_without_identity_headers(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/leads/import', [
            'file' => UploadedFile::fake()->createWithContent('leads.csv', "name,company\nJane Smith,Example Ltd\n"),
        ])->assertUnprocessable();
    }
}
