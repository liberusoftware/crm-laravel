<?php

declare(strict_types=1);

namespace Liberu\CRM\Collaboration\Actions;

use Liberu\CRM\Collaboration\Models\CollaborationRecord;

final class AddCollaborationRecord
{
    public function execute(int $teamId, string $recordKey, string $authorKey, string $body, string $kind = 'comment', array $mentions = []): CollaborationRecord
    {
        abort_unless($recordKey !== '' && $authorKey !== '' && trim($body) !== '' && in_array($kind, ['comment', 'mention', 'channel', 'approval', 'activity'], true), 422);

        return CollaborationRecord::query()->create(['team_id' => $teamId, 'record_key' => $recordKey, 'author_key' => $authorKey, 'body' => $body, 'kind' => $kind, 'mentions' => $mentions]);
    }
}
