<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $program_id
 * @property Carbon|null $qualified_at
 */
final class Referral extends Model
{
    protected $table = 'crm_referrals';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['attributed_at' => 'datetime', 'qualified_at' => 'datetime'];
    }
}
