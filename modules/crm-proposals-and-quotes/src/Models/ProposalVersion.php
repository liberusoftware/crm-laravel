<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Models;

use Illuminate\Database\Eloquent\Model;

final class ProposalVersion extends Model
{
    protected $table = 'crm_proposal_versions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['scope' => 'array', 'line_items' => 'array', 'options' => 'array', 'version' => 'integer'];
    }
}
