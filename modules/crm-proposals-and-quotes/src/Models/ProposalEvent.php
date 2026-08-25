<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Models;

use Illuminate\Database\Eloquent\Model;

final class ProposalEvent extends Model
{
    protected $table = 'crm_proposal_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }
}
