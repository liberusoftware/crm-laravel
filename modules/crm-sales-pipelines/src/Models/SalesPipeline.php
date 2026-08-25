<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Models;

use Illuminate\Database\Eloquent\Model;

final class SalesPipeline extends Model
{
    protected $table = 'crm_sales_pipelines';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
}
