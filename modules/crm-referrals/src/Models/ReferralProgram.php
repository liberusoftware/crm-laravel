<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $code_prefix
 * @property float $reward_amount
 * @property string $reward_currency
 */
final class ReferralProgram extends Model
{
    protected $table = 'crm_referral_programs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['reward_amount' => 'float', 'active' => 'boolean', 'rules' => 'array'];
    }
}
