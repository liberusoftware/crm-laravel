<?php

declare(strict_types=1);

namespace Liberu\CRM\Loyalty\Models;

use Illuminate\Database\Eloquent\Model;

final class LoyaltyRuleReward extends Model
{
    protected $table = 'crm_loyalty_rules_rewards';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['points' => 'decimal:2', 'conditions' => 'array', 'metadata' => 'array'];
    }
}
