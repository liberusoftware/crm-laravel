<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $currency @property string $balance @property string $threshold @property string $reload_amount @property string $status @property int $version */
final class UsageWallet extends Model
{
    protected $table = 'crm_usage_wallets';

    protected $fillable = ['team_id', 'currency', 'balance', 'threshold', 'reload_amount', 'status', 'version'];

    protected function casts(): array
    {
        return ['team_id' => 'integer', 'balance' => 'decimal:6', 'threshold' => 'decimal:6', 'reload_amount' => 'decimal:6', 'version' => 'integer'];
    }
}
