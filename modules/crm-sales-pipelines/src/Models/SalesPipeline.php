<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class SalesPipeline extends Model
{
    protected $table = 'crm_sales_pipelines';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function stages(): HasMany
    {
        return $this->hasMany(SalesStage::class, 'pipeline_id')->orderBy('position');
    }
}
