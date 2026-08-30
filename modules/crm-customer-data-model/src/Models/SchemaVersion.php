<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SchemaVersion extends Model
{
    protected $table = 'crm_customer_data_schema_versions';

    protected $fillable = ['object_id', 'version', 'status', 'snapshot', 'published_by', 'published_at'];

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'version' => 'integer', 'published_at' => 'datetime'];
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(ObjectDefinition::class, 'object_id');
    }
}
