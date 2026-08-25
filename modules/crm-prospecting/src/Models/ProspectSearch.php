<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Models;

use Illuminate\Database\Eloquent\Model;

final class ProspectSearch extends Model
{
    protected $table = 'crm_prospect_searches';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['filters' => 'array', 'result_count' => 'integer', 'completed_at' => 'datetime'];
    }
}
