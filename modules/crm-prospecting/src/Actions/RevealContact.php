<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Prospecting\Events\ProspectRevealed;
use Liberu\CRM\Prospecting\Models\Prospect;
use Liberu\CRM\Prospecting\Models\ProspectCredit;
use Liberu\CRM\Prospecting\Services\ProspectingPolicy;

final class RevealContact
{
    public function __construct(private readonly ProspectingPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ProspectCredit
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['prospect_id' => ['required', 'integer'], 'kind' => ['required', 'in:email,phone'], 'idempotency_key' => ['required', 'string', 'max:255']])->validate();
        $existing = ProspectCredit::query()->where('idempotency_key', $data['idempotency_key'])->first();
        if ($existing !== null) {
            abort_unless((int) $existing->team_id === $teamId, 403);

            return $existing;
        } $prospect = Prospect::query()->where('team_id', $teamId)->findOrFail($data['prospect_id']);
        $credit = ProspectCredit::query()->create(['team_id' => $teamId, 'user_id' => $userId, ...$data, 'revealed_at' => now()]);
        event(new ProspectRevealed($prospect));

        return $credit;
    }
}
