<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $key
 * @property string $label
 * @property string $type
 * @property array<string, mixed>|null $config
 * @property bool $is_required
 * @property bool $is_calculated
 * @property string|null $calculation
 * @property list<string>|null $required_stages
 */
final class FieldDefinition extends Model
{
    protected $table = 'crm_customer_data_fields';

    protected $fillable = ['object_id', 'key', 'label', 'type', 'description', 'config', 'is_required', 'is_calculated', 'calculation', 'required_stages', 'position'];

    protected function casts(): array
    {
        return ['config' => 'array', 'required_stages' => 'array', 'is_required' => 'boolean', 'is_calculated' => 'boolean', 'position' => 'integer'];
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(ObjectDefinition::class, 'object_id');
    }
}
