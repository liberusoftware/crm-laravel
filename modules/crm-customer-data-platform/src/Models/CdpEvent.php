<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatform\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $profile_id @property bool $consented */
final class CdpEvent extends Model
{
    protected $table = 'crm_cdp_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array', 'consented' => 'boolean', 'occurred_at' => 'datetime'];
    }
}
