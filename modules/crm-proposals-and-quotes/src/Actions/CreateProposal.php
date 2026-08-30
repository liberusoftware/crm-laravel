<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Actions;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Liberu\CRM\ProposalsAndQuotes\Models\Proposal;
use Liberu\CRM\ProposalsAndQuotes\Services\ProposalPolicy;

final class CreateProposal
{
    public function __construct(private readonly ProposalPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): Proposal
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['template_id' => ['nullable', 'integer'], 'customer_id' => ['nullable', 'integer'], 'title' => ['required', 'string', 'max:255'], 'currency' => ['required', 'string', 'size:3'], 'expires_at' => ['nullable', 'date']])->validate();

        return Proposal::query()->create(['team_id' => $teamId, ...$data, 'access_token' => Str::random(64)]);
    }
}
