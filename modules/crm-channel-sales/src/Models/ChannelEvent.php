<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSales\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $opportunity_id @property string $type @property float|null $commission */
final class ChannelEvent extends Model
{
    use IsTenantModel;

    protected $table = 'crm_channel_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['commission' => 'float', 'payload' => 'array'];
    }
}
