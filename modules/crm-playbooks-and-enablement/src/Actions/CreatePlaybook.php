<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\PlaybooksAndEnablement\Models\Playbook;
use Liberu\CRM\PlaybooksAndEnablement\Services\PlaybookPolicy;

final class CreatePlaybook
{
    public function __construct(private readonly PlaybookPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): Playbook
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:255'], 'kind' => ['required', 'in:script,qualification,battlecard,onboarding,coaching'], 'description' => ['nullable', 'string'], 'steps' => ['required', 'array']])->validate();

        return Playbook::query()->create(['team_id' => $teamId, ...$data]);
    }
}
