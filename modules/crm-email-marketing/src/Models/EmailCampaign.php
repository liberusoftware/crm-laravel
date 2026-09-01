<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketing\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $status @property string $content_type */
final class EmailCampaign extends Model
{
    use IsTenantModel;

    protected $table = 'crm_email_marketing_campaigns';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['personalization' => 'array', 'dynamic_content' => 'array', 'deliverability' => 'array', 'scheduled_at' => 'datetime'];
    }
}
