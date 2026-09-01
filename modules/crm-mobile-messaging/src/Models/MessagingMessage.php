<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessaging\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property string $direction @property string $status @property int $team_id */
final class MessagingMessage extends Model
{
    use IsTenantModel;

    protected $table = 'crm_mobile_messaging_messages';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array', 'sent_at' => 'datetime'];
    }
}
