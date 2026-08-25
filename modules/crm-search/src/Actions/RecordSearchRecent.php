<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearch\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\CrmSearch\Models\SearchRecent;
use Liberu\CRM\CrmSearch\Services\CrmSearchPolicy;

final class RecordSearchRecent
{
    public function __construct(private readonly CrmSearchPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): SearchRecent
    {
        abort_unless($this->policy->canSearch($teamId, $userId), 403);
        $data = Validator::make($input, ['record_type' => ['required', 'string', 'max:80'], 'record_key' => ['required', 'string', 'max:160'], 'title' => ['required', 'string', 'max:255']])->validate();

        return SearchRecent::query()->create(['team_id' => $teamId, 'user_id' => $userId, 'viewed_at' => now(), ...$data]);
    }
}
