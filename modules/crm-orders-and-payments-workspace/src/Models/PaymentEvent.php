<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspace\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $kind
 * @property string $status
 */
final class PaymentEvent extends Model
{
    protected $table = 'crm_orders_payments_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'payload' => 'array'];
    }
}
