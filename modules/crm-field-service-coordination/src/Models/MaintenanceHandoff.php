<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordination\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $appointment_id @property string $status */
final class MaintenanceHandoff extends Model
{
    use IsTenantModel;

    protected $table = 'crm_field_service_handoffs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array', 'handed_off_at' => 'datetime'];
    }
}
