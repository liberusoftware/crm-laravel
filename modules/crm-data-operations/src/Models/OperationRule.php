<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Models;

use Illuminate\Database\Eloquent\Model;

final class OperationRule extends Model
{
    protected $table = 'crm_data_operation_rules';

    protected $fillable = ['team_id', 'name', 'kind', 'definition', 'active'];

    protected function casts(): array
    {
        return ['definition' => 'array', 'active' => 'boolean'];
    }
}
