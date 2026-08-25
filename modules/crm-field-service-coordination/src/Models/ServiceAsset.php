<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordination\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id */
final class ServiceAsset extends Model
{
    protected $table = 'crm_field_service_assets';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
