<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessaging\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $address @property string $channel @property string $consent */
final class MessagingContact extends Model
{
    use IsTenantModel;

    protected $table = 'crm_mobile_messaging_contacts';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['consent_at' => 'datetime', 'metadata' => 'array'];
    }
}
