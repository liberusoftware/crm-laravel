<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordination\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id */
final class ServiceAsset extends Model
{
    use IsTenantModel;

    protected $table = 'crm_field_service_assets';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
