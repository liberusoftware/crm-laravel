<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGateway\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property string $key
 * @property string $kind
 * @property string $provider
 * @property string $status
 */
final class GatewayChannel extends Model
{
    use IsTenantModel;

    protected $table = 'crm_gateway_channels';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['configuration' => 'array', 'health' => 'array'];
    }
}
