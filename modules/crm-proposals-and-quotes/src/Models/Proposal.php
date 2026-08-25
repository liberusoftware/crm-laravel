<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Models;

use Illuminate\Database\Eloquent\Model;

final class Proposal extends Model
{
    protected $table = 'crm_proposals';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['total' => 'float', 'expires_at' => 'date'];
    }
}
