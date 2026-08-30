<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Queries;

use Liberu\CRM\ProposalsAndQuotes\Models\Proposal;
use Liberu\CRM\ProposalsAndQuotes\Models\ProposalTemplate;
use Liberu\CRM\ProposalsAndQuotes\Models\ProposalVersion;

final class ProposalQuery
{
    public function templates(int $teamId)
    {
        return ProposalTemplate::query()->where('team_id', $teamId)->latest();
    }

    public function proposals(int $teamId)
    {
        return Proposal::query()->where('team_id', $teamId)->latest();
    }

    public function versions(int $teamId)
    {
        return ProposalVersion::query()->where('team_id', $teamId)->latest();
    }
}
