<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ReputationManagement\Models\ReputationTemplate;
use Liberu\CRM\ReputationManagement\Services\ReputationPolicy;

final class SaveTemplate
{
    public function __construct(private readonly ReputationPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ReputationTemplate
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['id' => ['nullable', 'integer'], 'name' => ['required', 'string', 'max:255'], 'channel' => ['required', 'in:email,sms,whatsapp'], 'content' => ['required', 'string'], 'active' => ['boolean']])->validate();
        $id = $data['id'] ?? null;
        unset($data['id']);

        return ReputationTemplate::query()->updateOrCreate(['id' => $id, 'team_id' => $teamId], ['team_id' => $teamId, ...$data]);
    }
}
