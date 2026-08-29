<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $status
 * @property string|null $classification
 * @property float|null $confidence
 * @property string|null $response_draft
 * @property array<string,mixed>|null $resolution_plan
 * @property int $escalation_level
 * @property array<string,mixed>|null $metadata
 */
final class AgentCase extends Model
{
    protected $table = 'crm_service_agent_cases';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['confidence' => 'float', 'resolution_plan' => 'array', 'metadata' => 'array'];
    }
}
