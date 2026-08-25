<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Models;

use Illuminate\Database\Eloquent\Model; /** @property int $workload @property bool $active */
final class RoutingAgent extends Model
{
    protected $table = 'crm_routing_agents';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['territories' => 'array', 'skills' => 'array', 'languages' => 'array', 'availability' => 'array', 'value_capacity' => 'float', 'last_assigned_at' => 'datetime', 'active' => 'boolean'];
    }
}
