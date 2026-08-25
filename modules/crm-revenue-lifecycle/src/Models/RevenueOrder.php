<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Models;

use Illuminate\Database\Eloquent\Model;

final class RevenueOrder extends Model
{
    protected $table = 'crm_revenue_orders';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'float'];
    }
}
