<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ProposalsAndQuotes\Models\Proposal;
use Liberu\CRM\ProposalsAndQuotes\Models\ProposalEvent;
use Liberu\CRM\ProposalsAndQuotes\Services\ProposalPolicy;

final class AddProposalComment
{
    public function __construct(private readonly ProposalPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ProposalEvent
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['proposal_id' => ['required', 'integer'], 'version_id' => ['nullable', 'integer'], 'comment' => ['required', 'string', 'max:5000']])->validate();
        Proposal::query()->where('team_id', $teamId)->findOrFail($data['proposal_id']);

        return ProposalEvent::query()->create(['team_id' => $teamId, ...$data, 'actor_id' => $userId, 'type' => 'commented', 'occurred_at' => now()]);
    }
}
