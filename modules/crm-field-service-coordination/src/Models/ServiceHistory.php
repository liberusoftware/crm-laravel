<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordination\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $appointment_id */
final class ServiceHistory extends Model
{
    protected $table = 'crm_field_service_history';

    protected $guarded = [];
}
