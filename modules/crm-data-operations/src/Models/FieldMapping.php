<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $operation_id */
final class FieldMapping extends Model
{
    protected $table = 'crm_data_operation_mappings';

    protected $fillable = ['operation_id', 'source_field', 'target_field', 'transform', 'required'];

    protected function casts(): array
    {
        return ['transform' => 'array', 'required' => 'boolean'];
    }
}
