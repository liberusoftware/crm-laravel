<?php

declare(strict_types=1);

namespace Liberu\CRM\Documents\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Documents\Models\CrmDocument;
use Liberu\CRM\Documents\Models\DocumentVersion;
use Liberu\CRM\Documents\Services\DocumentsPolicy;

final class CreateDocumentVersion
{
    public function __construct(private readonly DocumentsPolicy $policy) {}

    public function execute(int $teamId, int $userId, CrmDocument $document, array $input): DocumentVersion
    {
        abort_unless($document->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['storage_key' => ['required', 'string', 'max:255'], 'checksum' => ['nullable', 'string', 'max:255']])->validate();
        $version = (int) DocumentVersion::query()->where('document_id', $document->id)->max('version') + 1;

        return DocumentVersion::query()->create(['team_id' => $teamId, 'document_id' => $document->id, 'actor_id' => $userId, 'version' => $version, ...$data]);
    }
}
