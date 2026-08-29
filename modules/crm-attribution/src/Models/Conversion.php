<?php

declare(strict_types=1);

namespace Liberu\CRM\Attribution\Models;

use Illuminate\Database\Eloquent\Model;

final class Conversion extends Model
{
    protected $table = 'crm_attribution_conversions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'decimal:2', 'allocations' => 'array', 'converted_at' => 'datetime'];
    }
}
