<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspace\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int $team_id @property string $kind @property string $status @property float $amount @property float $paid_amount @property float $refunded_amount */
final class PaymentTransaction extends Model
{
    use IsTenantModel;

    protected $table = 'crm_orders_payments';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'paid_amount' => 'decimal:2', 'refunded_amount' => 'decimal:2', 'metadata' => 'array'];
    }

    public function events(): HasMany
    {
        return $this->hasMany(PaymentEvent::class, 'transaction_id');
    }
}
