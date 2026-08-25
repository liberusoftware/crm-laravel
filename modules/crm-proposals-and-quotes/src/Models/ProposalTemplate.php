<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Models;

use Illuminate\Database\Eloquent\Model;

final class ProposalTemplate extends Model
{
    protected $table = 'crm_proposal_templates';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['branding' => 'array', 'sections' => 'array', 'active' => 'boolean'];
    }
}
