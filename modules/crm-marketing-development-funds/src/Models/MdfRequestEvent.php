<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFunds\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $kind
 * @property string $status
 */
final class MdfRequestEvent extends Model
{
    protected $table = 'crm_mdf_request_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'metadata' => 'array'];
    }
}
