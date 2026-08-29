<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ProposalsAndQuotes\Models\ProposalTemplate;
use Liberu\CRM\ProposalsAndQuotes\Services\ProposalPolicy;

final class CreateProposalTemplate
{
    public function __construct(private readonly ProposalPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ProposalTemplate
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:255'], 'branding' => ['nullable', 'array'], 'sections' => ['nullable', 'array']])->validate();

        return ProposalTemplate::query()->create(['team_id' => $teamId, ...$data]);
    }
}
