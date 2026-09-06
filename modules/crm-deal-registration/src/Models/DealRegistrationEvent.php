<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistration\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $deal_id @property string $event */
final class DealRegistrationEvent extends Model
{
    use IsTenantModel;

    protected $table = 'crm_deal_registration_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'occurred_at' => 'datetime'];
    }
}
