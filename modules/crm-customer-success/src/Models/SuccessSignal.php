<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccess\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $customer_id */
final class SuccessSignal extends Model
{
    protected $table = 'crm_success_signals';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'decimal:4', 'metadata' => 'array', 'observed_at' => 'datetime'];
    }
}
