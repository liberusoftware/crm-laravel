<?php

declare(strict_types=1);

namespace Liberu\CRM\Knowledge\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Knowledge\Models\KnowledgeArticle;
use Liberu\CRM\Knowledge\Services\KnowledgePolicy;

final class CreateArticle
{
    public function __construct(private readonly KnowledgePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): KnowledgeArticle
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['slug' => ['required', 'string', 'max:255'], 'visibility' => ['required', 'in:internal,public'], 'status' => ['nullable', 'in:draft,in_review,published,archived'], 'category' => ['nullable', 'string', 'max:255'], 'locale' => ['required', 'string', 'size:2'], 'title' => ['required', 'string', 'max:255'], 'body' => ['required', 'string'], 'stale_at' => ['nullable', 'date'], 'metadata' => ['nullable', 'array']])->validate();

        return KnowledgeArticle::query()->create(['team_id' => $teamId, ...$data]);
    }
}
