<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Models;

use Illuminate\Database\Eloquent\Model;

final class CommissionExport extends Model
{
    protected $table = 'crm_commission_exports';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }
}
