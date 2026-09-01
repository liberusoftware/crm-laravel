<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessaging\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int $team_id @property string $channel @property string $status */
final class MessagingCampaign extends Model
{
    use IsTenantModel;

    protected $table = 'crm_mobile_messaging_campaigns';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['quiet_start' => 'datetime', 'quiet_end' => 'datetime', 'metadata' => 'array'];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MessagingMessage::class, 'campaign_id');
    }
}
