<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Personalization\Events\PersonalizationDecided;
use Liberu\CRM\Personalization\Models\PersonalizationDecision;
use Liberu\CRM\Personalization\Models\PersonalizationRule;
use Liberu\CRM\Personalization\Services\PersonalizationPolicy;

final class DecidePersonalization
{
    public function __construct(private readonly PersonalizationPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): PersonalizationDecision
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['rule_id' => ['required', 'integer'], 'subject_type' => ['required', 'string', 'max:255'], 'subject_id' => ['required', 'integer'], 'channel' => ['required', 'string', 'max:50'], 'locale' => ['required', 'string', 'max:20'], 'attributes' => ['nullable', 'array'], 'consent' => ['required', 'boolean']])->validate();
        $rule = PersonalizationRule::query()->where('team_id', $teamId)->where('status', 'active')->findOrFail($data['rule_id']);
        $consented = (bool) $data['consent'];
        unset($data['consent']);
        $bucket = hexdec(substr(hash('sha256', $teamId.':'.$data['subject_type'].':'.$data['subject_id'].':'.$rule->id), 0, 8)) % 100;
        $holdout = ! $consented || $bucket < $rule->holdout_percent;
        $variants = $rule->variants;
        $variant = $holdout ? ($rule->fallback['variant'] ?? array_key_first($variants)) : array_key_first($variants);
        $decision = PersonalizationDecision::query()->create(['team_id' => $teamId, ...$data, 'variant' => (string) $variant, 'holdout' => $holdout, 'decided_at' => now()]);
        event(new PersonalizationDecided($decision));

        return $decision;
    }
}
