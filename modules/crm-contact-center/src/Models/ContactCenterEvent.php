<?php

declare(strict_types=1);

namespace Liberu\CRM\ContactCenter\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $agent_id @property string $type @property string $status @property int|null $sla_seconds @property int|null $wait_seconds */
final class ContactCenterEvent extends Model
{
    use IsTenantModel;

    protected $table = 'crm_contact_center_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
