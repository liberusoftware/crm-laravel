<?php

declare(strict_types=1);

namespace Liberu\CRM\Loyalty\Models;

use Illuminate\Database\Eloquent\Model;

/** @property string $kind @property float $points */
final class LoyaltyLedgerEntry extends Model
{
    protected $table = 'crm_loyalty_ledger';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['points' => 'decimal:2', 'expires_on' => 'date', 'metadata' => 'array'];
    }
}
