<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketing\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $campaign_id @property string $status */
final class EmailDelivery extends Model
{
    use IsTenantModel;

    protected $table = 'crm_email_marketing_deliveries';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }
}
