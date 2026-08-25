<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatform\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $profile_key @property array|null $consent */
final class CdpProfile extends Model
{
    protected $table = 'crm_cdp_profiles';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['attributes' => 'array', 'consent' => 'array'];
    }
}
