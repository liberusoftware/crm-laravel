<?php

declare(strict_types=1);

namespace Liberu\CRM\Documents\Actions;

use Illuminate\Support\Str;
use Liberu\CRM\Documents\Models\CrmDocument;
use Liberu\CRM\Documents\Models\DocumentLink;
use Liberu\CRM\Documents\Services\DocumentsPolicy;

final class CreateDocumentLink
{
    public function __construct(private readonly DocumentsPolicy $policy) {}

    public function execute(int $teamId, int $userId, CrmDocument $document, ?string $expiresAt = null): DocumentLink
    {
        abort_unless($document->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);

        return DocumentLink::query()->create(['team_id' => $teamId, 'document_id' => $document->id, 'token' => (string) Str::uuid(), 'expires_at' => $expiresAt]);
    }
}
