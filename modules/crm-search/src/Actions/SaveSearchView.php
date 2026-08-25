<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearch\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\CrmSearch\Models\SearchView;
use Liberu\CRM\CrmSearch\Services\CrmSearchPolicy;

final class SaveSearchView
{
    public function __construct(private readonly CrmSearchPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): SearchView
    {
        abort_unless($this->policy->canSearch($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:120'], 'record_type' => ['required', 'string', 'max:80'], 'filters' => ['required', 'array'], 'shared' => ['nullable', 'boolean']])->validate();

        return SearchView::query()->create(['team_id' => $teamId, 'user_id' => $userId, ...$data]);
    }
}
