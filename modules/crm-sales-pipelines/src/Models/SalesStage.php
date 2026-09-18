<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $probability
 * @property int|null $rotting_days
 */
final class SalesStage extends Model
{
    protected $table = 'crm_sales_stages';

    protected $guarded = [];

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(SalesPipeline::class, 'pipeline_id');
    }
}
