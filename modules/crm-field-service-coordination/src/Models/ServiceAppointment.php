<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordination\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $work_type_id @property int|null $technician_id @property string $status */
final class ServiceAppointment extends Model
{
    protected $table = 'crm_field_service_appointments';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'dispatch' => 'array'];
    }
}
