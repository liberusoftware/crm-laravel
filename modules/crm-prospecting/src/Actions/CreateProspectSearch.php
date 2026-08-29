<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Prospecting\Models\ProspectSearch;
use Liberu\CRM\Prospecting\Services\ProspectingPolicy;

final class CreateProspectSearch
{
    public function __construct(private readonly ProspectingPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ProspectSearch
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['profile_id' => ['nullable', 'integer'], 'provider' => ['required', 'string', 'max:100'], 'filters' => ['required', 'array']])->validate();

        return ProspectSearch::query()->create(['team_id' => $teamId, ...$data]);
    }
}
