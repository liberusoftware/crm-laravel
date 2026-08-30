<?php

declare(strict_types=1);

namespace Liberu\CRM\Knowledge\Queries;

use Liberu\CRM\Knowledge\Models\KnowledgeArticle;

final class KnowledgeQuery
{
    public function forTeam(int $teamId, ?string $search = null)
    {
        $query = KnowledgeArticle::query()->where('team_id', $teamId)->with(['versions', 'events']);
        if ($search !== null && $search !== '') {
            $query->where(fn ($q) => $q->where('title', 'like', '%'.$search.'%')->orWhere('body', 'like', '%'.$search.'%'));
        }

        return $query->latest();
    }
}
