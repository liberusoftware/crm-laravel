<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearch\Queries;

use Liberu\CRM\CrmSearch\Models\SearchDocument;
use Liberu\CRM\CrmSearch\Models\SearchRecent;
use Liberu\CRM\CrmSearch\Models\SearchView;

final class CrmSearchQuery
{
    public function search(int $teamId, string $term)
    {
        $term = trim($term);
        $query = SearchDocument::query()->where('team_id', $teamId);

        if ($term === '') {
            return $query->whereKey(0);
        }

        return $query
            ->where(fn ($builder) => $builder->where('title', 'like', '%'.$term.'%')->orWhere('content', 'like', '%'.$term.'%'))
            ->orderByRaw('CASE WHEN title LIKE ? THEN 0 WHEN title LIKE ? THEN 1 ELSE 2 END', [$term, $term.'%'])
            ->latest('indexed_at');
    }

    public function views(int $teamId, int $userId)
    {
        return SearchView::query()->where('team_id', $teamId)->where(fn ($query) => $query->where('shared', true)->orWhere('user_id', $userId))->latest();
    }

    public function recents(int $teamId, int $userId)
    {
        return SearchRecent::query()->where('team_id', $teamId)->where('user_id', $userId)->latest('viewed_at');
    }
}
