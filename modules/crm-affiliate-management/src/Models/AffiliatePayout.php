<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagement\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property float $amount
 * @property string $status
 */
final class AffiliatePayout extends Model
{
    protected $table = 'crm_affiliate_payouts';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'dispute' => 'array', 'assets' => 'array', 'approved_at' => 'datetime'];
    }
}
