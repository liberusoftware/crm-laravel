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
        return SearchDocument::query()->where('team_id', $teamId)->where(fn ($query) => $query->where('title', 'like', '%'.$term.'%')->orWhere('content', 'like', '%'.$term.'%'))->latest('indexed_at');
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
