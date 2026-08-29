<?php

declare(strict_types=1);

namespace Liberu\CRM\Documents\Queries;

use Liberu\CRM\Documents\Models\CrmDocument;

final class DocumentsQuery
{
    public function documents(int $teamId)
    {
        return CrmDocument::query()->where('team_id', $teamId)->where('status', 'active')->latest();
    }
}
