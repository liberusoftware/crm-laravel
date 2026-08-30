<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RelationshipDefinition extends Model
{
    protected $table = 'crm_customer_data_relationships';

    protected $fillable = ['team_id', 'from_object_id', 'to_object_id', 'key', 'label', 'cardinality', 'config'];

    protected function casts(): array
    {
        return ['config' => 'array'];
    }

    public function fromObject(): BelongsTo
    {
        return $this->belongsTo(ObjectDefinition::class, 'from_object_id');
    }

    public function toObject(): BelongsTo
    {
        return $this->belongsTo(ObjectDefinition::class, 'to_object_id');
    }
}
