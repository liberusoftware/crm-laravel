<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Models;

use Illuminate\Database\Eloquent\Model;

/** @property float $probability */
final class SalesStage extends Model
{
    protected $table = 'crm_sales_stages';

    protected $guarded = [];
}
