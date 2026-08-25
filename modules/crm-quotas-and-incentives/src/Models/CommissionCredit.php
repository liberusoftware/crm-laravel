<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id */
final class CommissionCredit extends Model
{
    protected $table = 'crm_commission_credits';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'float', 'commission' => 'float'];
    }
}
