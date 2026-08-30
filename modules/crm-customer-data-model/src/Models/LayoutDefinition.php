<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LayoutDefinition extends Model
{
    protected $table = 'crm_customer_data_layouts';

    protected $fillable = ['object_id', 'key', 'label', 'sections', 'is_default'];

    protected function casts(): array
    {
        return ['sections' => 'array', 'is_default' => 'boolean'];
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(ObjectDefinition::class, 'object_id');
    }
}
