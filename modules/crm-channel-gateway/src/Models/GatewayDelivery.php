<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGateway\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $channel_id @property string $status @property int $attempts */
final class GatewayDelivery extends Model
{
    use IsTenantModel;

    protected $table = 'crm_gateway_deliveries';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
