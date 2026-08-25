<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelService\Models;

use Illuminate\Database\Eloquent\Model;

/** @property string $direction @property string $body @property int $team_id */
final class Interaction extends Model
{
    protected $table = 'crm_omnichannel_interactions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'occurred_at' => 'datetime'];
    }
}
