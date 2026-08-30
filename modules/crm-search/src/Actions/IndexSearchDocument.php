<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearch\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\CrmSearch\Models\SearchDocument;
use Liberu\CRM\CrmSearch\Services\CrmSearchPolicy;

final class IndexSearchDocument
{
    public function __construct(private readonly CrmSearchPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): SearchDocument
    {
        abort_unless($this->policy->canSearch($teamId, $userId), 403);
        $data = Validator::make($input, ['record_type' => ['required', 'string', 'max:80'], 'record_key' => ['required', 'string', 'max:160'], 'title' => ['required', 'string', 'max:255'], 'content' => ['nullable', 'string'], 'attributes' => ['nullable', 'array']])->validate();

        return SearchDocument::query()->updateOrCreate(['team_id' => $teamId, 'record_type' => $data['record_type'], 'record_key' => $data['record_key']], ['indexed_at' => now(), ...$data]);
    }
}
