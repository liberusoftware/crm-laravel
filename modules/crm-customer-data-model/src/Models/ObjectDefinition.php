<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $team_id
 * @property string $key
 * @property string $label
 * @property string|null $description
 * @property bool $is_standard
 * @property string $status
 * @property int $current_version
 */
final class ObjectDefinition extends Model
{
    protected $table = 'crm_customer_data_objects';

    protected $fillable = ['team_id', 'key', 'label', 'description', 'is_standard', 'status', 'current_version'];

    protected function casts(): array
    {
        return ['is_standard' => 'boolean', 'current_version' => 'integer'];
    }

    /** @return HasMany<FieldDefinition, $this> */
    public function fields(): HasMany
    {
        return $this->hasMany(FieldDefinition::class, 'object_id');
    }

    /** @return HasMany<LayoutDefinition, $this> */
    public function layouts(): HasMany
    {
        return $this->hasMany(LayoutDefinition::class, 'object_id');
    }

    /** @return HasMany<SchemaVersion, $this> */
    public function versions(): HasMany
    {
        return $this->hasMany(SchemaVersion::class, 'object_id');
    }
}
