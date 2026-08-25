<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Models;

use Illuminate\Database\Eloquent\Model;

final class Prospect extends Model
{
    protected $table = 'crm_prospects';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['provenance' => 'array', 'metadata' => 'array'];
    }
}
