<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelService\Models;

use Illuminate\Database\Eloquent\Model;

final class Macro extends Model
{
    protected $table = 'crm_omnichannel_macros';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
}
