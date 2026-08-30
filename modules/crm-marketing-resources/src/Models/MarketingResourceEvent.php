<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingResources\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $kind
 * @property string $status
 */
final class MarketingResourceEvent extends Model
{
    protected $table = 'crm_marketing_resource_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime'];
    }
}
