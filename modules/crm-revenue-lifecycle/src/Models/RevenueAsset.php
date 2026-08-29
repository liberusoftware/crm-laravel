<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Models;

use Illuminate\Database\Eloquent\Model; /** @property string $status @property string|null $lifecycle_action @property \Illuminate\Support\Carbon|null $renewal_date */

/**
 * @property int $team_id
 */
final class RevenueAsset extends Model
{
    protected $table = 'crm_revenue_assets';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['renewal_date' => 'date', 'entitlements' => 'array', 'usage_signals' => 'array', 'metadata' => 'array'];
    }
}
