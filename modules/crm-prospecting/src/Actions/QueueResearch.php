<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Prospecting\Models\Prospect;
use Liberu\CRM\Prospecting\Models\ProspectResearchItem;
use Liberu\CRM\Prospecting\Services\ProspectingPolicy;

final class QueueResearch
{
    public function __construct(private readonly ProspectingPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ProspectResearchItem
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['prospect_id' => ['required', 'integer'], 'priority' => ['required', 'in:low,normal,high'], 'notes' => ['nullable', 'string']])->validate();
        Prospect::query()->where('team_id', $teamId)->findOrFail($data['prospect_id']);

        return ProspectResearchItem::query()->firstOrCreate(['team_id' => $teamId, 'prospect_id' => $data['prospect_id'], 'status' => 'queued'], ['team_id' => $teamId, ...$data]);
    }
}
