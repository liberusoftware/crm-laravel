<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Models;

use Illuminate\Database\Eloquent\Model;

final class RoutingRule extends Model
{
    protected $table = 'crm_routing_rules';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['conditions' => 'array', 'action' => 'array', 'active' => 'boolean'];
    }
}
