<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Models;

use Illuminate\Database\Eloquent\Model;

final class UsageCharge extends Model
{
    protected $table = 'crm_usage_charges';

    protected $fillable = ['team_id', 'usage_import_id', 'wallet_id', 'client_reference', 'cost', 'charge', 'currency', 'status'];

    protected function casts(): array
    {
        return ['cost' => 'decimal:6', 'charge' => 'decimal:6'];
    }
}
