<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $team_id
 * @property string $kind
 * @property string $status
 */
final class DataOperation extends Model
{
    protected $table = 'crm_data_operations';

    protected $fillable = ['team_id', 'actor_id', 'kind', 'status', 'source', 'target', 'options', 'total_rows', 'processed_rows', 'failed_rows', 'failure_reason', 'started_at', 'completed_at'];

    protected function casts(): array
    {
        return ['options' => 'array', 'total_rows' => 'integer', 'processed_rows' => 'integer', 'failed_rows' => 'integer', 'started_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    /** @return HasMany<FieldMapping, $this> */
    public function mappings(): HasMany
    {
        return $this->hasMany(FieldMapping::class, 'operation_id');
    }

    /** @return HasMany<OperationException, $this> */
    public function exceptions(): HasMany
    {
        return $this->hasMany(OperationException::class, 'operation_id');
    }

    /** @return HasMany<DuplicateMatch, $this> */
    public function duplicates(): HasMany
    {
        return $this->hasMany(DuplicateMatch::class, 'operation_id');
    }
}
