<?php

declare(strict_types=1);

namespace Liberu\CRM\Documents\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Documents\Models\CrmDocument;
use Liberu\CRM\Documents\Models\DocumentEvent;

final class RecordDocumentEngagement
{
    public function execute(int $teamId, ?int $actorId, CrmDocument $document, array $input): DocumentEvent
    {
        abort_unless($document->team_id === $teamId, 403);
        $data = Validator::make($input, ['event' => ['required', 'in:viewed,downloaded,shared,expired'], 'metadata' => ['nullable', 'array']])->validate();

        return DocumentEvent::query()->create(['team_id' => $teamId, 'document_id' => $document->id, 'actor_id' => $actorId, 'occurred_at' => now(), ...$data]);
    }
}
