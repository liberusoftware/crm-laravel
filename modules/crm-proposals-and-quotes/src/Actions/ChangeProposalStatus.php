<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Actions;

use Liberu\CRM\ProposalsAndQuotes\Events\ProposalStatusChanged;
use Liberu\CRM\ProposalsAndQuotes\Models\Proposal;
use Liberu\CRM\ProposalsAndQuotes\Services\ProposalPolicy;

final class ChangeProposalStatus
{
    public function __construct(private readonly ProposalPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $proposalId, string $status): Proposal
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        abort_unless(in_array($status, ['approved', 'delivered', 'accepted', 'rejected', 'expired'], true), 422);
        $proposal = Proposal::query()->where('team_id', $teamId)->findOrFail($proposalId);
        $proposal->update(['status' => $status]);
        event(new ProposalStatusChanged($proposal, $status));

        return $proposal->refresh();
    }
}
