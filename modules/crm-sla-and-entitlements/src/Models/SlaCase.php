<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int|null $entitlement_id
 * @property string $status
 * @property Carbon|null $responded_at
 * @property Carbon|null $resolved_at
 * @property Carbon|null $resolution_due_at
 * @property int $paused_minutes
 * @property array<string,mixed>|null $metadata
 */
final class SlaCase extends Model
{
    protected $table = 'crm_sla_cases';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['opened_at' => 'datetime', 'response_due_at' => 'datetime', 'resolution_due_at' => 'datetime', 'responded_at' => 'datetime', 'resolved_at' => 'datetime', 'metadata' => 'array'];
    }
}
