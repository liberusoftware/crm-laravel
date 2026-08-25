<?php

declare(strict_types=1);

namespace Tests\Feature\Documents;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Documents\Actions\CreateDocument;
use Liberu\CRM\Documents\Actions\CreateDocumentLink;
use Liberu\CRM\Documents\Actions\CreateDocumentVersion;
use Liberu\CRM\Documents\Actions\RecordDocumentEngagement;
use Tests\TestCase;

final class CrmDocumentsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_version_link_and_engagement_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $document = app(CreateDocument::class)->execute($team->id, $owner->id, ['name' => 'Proposal', 'kind' => 'template', 'storage_key' => 'proposals/p1.html', 'storage_provider' => 's3', 'retention_until' => '2027-01-01']);
        $version = app(CreateDocumentVersion::class)->execute($team->id, $owner->id, $document, ['storage_key' => 'proposals/p1-v2.html', 'checksum' => 'abc']);
        $link = app(CreateDocumentLink::class)->execute($team->id, $owner->id, $document);
        $event = app(RecordDocumentEngagement::class)->execute($team->id, $owner->id, $document, ['event' => 'viewed']);
        $this->assertSame(1, $version->version);
        $this->assertNotEmpty($link->token);
        $this->assertSame('viewed', $event->event);
    }
}
