<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Models;

use Illuminate\Database\Eloquent\Model;

final class StageHistory extends Model
{
    protected $table = 'crm_sales_stage_history';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['entered_at' => 'datetime'];
    }
}
