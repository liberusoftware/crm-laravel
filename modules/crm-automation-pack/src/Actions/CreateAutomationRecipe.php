<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPack\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\AutomationPack\Models\AutomationRecipe;
use Liberu\CRM\AutomationPack\Services\AutomationPackPolicy;

final class CreateAutomationRecipe
{
    public function __construct(private readonly AutomationPackPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): AutomationRecipe
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:160'], 'triggers' => ['required', 'array'], 'conditions' => ['nullable', 'array'], 'actions' => ['required', 'array'], 'approval_required' => ['nullable', 'boolean']])->validate();

        return AutomationRecipe::query()->create(['team_id' => $teamId, 'owner_id' => $userId, 'status' => 'draft', 'version' => 1, ...$data]);
    }
}
