<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

final class UsageImport extends Model
{
    use IsTenantModel;

    protected $table = 'crm_usage_imports';

    protected $fillable = ['team_id', 'provider', 'external_id', 'amount', 'currency', 'status', 'failure_reason', 'payload'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:6', 'payload' => 'array'];
    }
}
