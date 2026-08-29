<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ProposalsAndQuotes\Models\Proposal;
use Liberu\CRM\ProposalsAndQuotes\Models\ProposalVersion;
use Liberu\CRM\ProposalsAndQuotes\Services\ProposalPolicy;

final class CreateProposalVersion
{
    public function __construct(private readonly ProposalPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ProposalVersion
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['proposal_id' => ['required', 'integer'], 'scope' => ['nullable', 'array'], 'line_items' => ['required', 'array'], 'options' => ['nullable', 'array']])->validate();
        $proposal = Proposal::query()->where('team_id', $teamId)->findOrFail($data['proposal_id']);
        $version = ((int) ProposalVersion::query()->where('team_id', $teamId)->where('proposal_id', $proposal->id)->max('version')) + 1;
        $total = collect($data['line_items'])->sum(static fn (array $item): float => (float) ($item['quantity'] ?? 1) * (float) ($item['unit_price'] ?? 0));
        $proposal->update(['total' => $total]);

        return ProposalVersion::query()->create(['team_id' => $teamId, ...$data, 'version' => $version]);
    }
}
