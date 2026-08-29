<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfService\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $profile_id @property string $kind */
final class SelfServiceReference extends Model
{
    protected $table = 'crm_self_service_references';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
