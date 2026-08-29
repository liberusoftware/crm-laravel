<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $status
 * @property int $pipeline_id
 * @property int $stage_id
 * @property float $value
 * @property float $probability
 * @property string|null $loss_reason
 * @property Carbon|null $last_stage_at
 * @property array<string,mixed>|null $products
 * @property array<string,mixed>|null $competitors
 * @property array<string,mixed>|null $dependencies
 */
final class Opportunity extends Model
{
    protected $table = 'crm_sales_opportunities';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'float', 'probability' => 'float', 'close_date' => 'date', 'last_stage_at' => 'datetime', 'products' => 'array', 'competitors' => 'array', 'dependencies' => 'array'];
    }
}
