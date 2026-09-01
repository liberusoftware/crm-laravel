<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketing\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $campaign_id @property string $event */
final class EmailMarketingEvent extends Model
{
    use IsTenantModel;

    protected $table = 'crm_email_marketing_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'occurred_at' => 'datetime'];
    }
}
