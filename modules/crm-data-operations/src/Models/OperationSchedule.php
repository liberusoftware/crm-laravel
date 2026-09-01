<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

final class OperationSchedule extends Model
{
    use IsTenantModel;

    protected $table = 'crm_data_operation_schedules';

    protected $fillable = ['team_id', 'operation_id', 'cron', 'timezone', 'active', 'next_run_at'];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'next_run_at' => 'datetime'];
    }
}
