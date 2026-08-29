<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessaging\Models;

use Illuminate\Database\Eloquent\Model;

final class MessagingTemplate extends Model
{
    protected $table = 'crm_mobile_messaging_templates';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
}
