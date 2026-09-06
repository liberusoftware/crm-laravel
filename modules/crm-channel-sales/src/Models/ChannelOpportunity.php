<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSales\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $owner_id
 * @property string $partner_key
 * @property string $opportunity_key
 * @property string $stage
 * @property float $amount
 * @property float $commission_rate
 * @property string $handoff_status
 */
final class ChannelOpportunity extends Model
{
    use IsTenantModel;

    protected $table = 'crm_channel_opportunities';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'float', 'commission_rate' => 'float', 'pricing_reference' => 'array', 'forecast' => 'array'];
    }
}
