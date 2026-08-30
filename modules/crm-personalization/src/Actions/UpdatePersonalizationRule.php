<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Personalization\Models\PersonalizationRule;
use Liberu\CRM\Personalization\Services\PersonalizationPolicy;

final class UpdatePersonalizationRule
{
    public function __construct(private readonly PersonalizationPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $ruleId, array $input): PersonalizationRule
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'kind' => ['required', 'in:content,offer,channel,send_time,locale,lifecycle'],
            'conditions' => ['required', 'array'],
            'variants' => ['required', 'array', 'min:1'],
            'fallback' => ['required', 'array'],
            'holdout_percent' => ['required', 'integer', 'between:0,100'],
        ])->validate();
        $rule = PersonalizationRule::query()->where('team_id', $teamId)->findOrFail($ruleId);
        $rule->update($data);

        return $rule->refresh();
    }
}
