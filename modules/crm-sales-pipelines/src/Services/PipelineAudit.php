<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Services;

use Illuminate\Support\Facades\Log;

final class PipelineAudit
{
    public function record(int $teamId, ?int $actorId, string $event, array $details = []): void
    {
        Log::info('crm.sales_pipeline.'.$event, ['team_id' => $teamId, 'actor_id' => $actorId, 'details' => $details]);
    }
}
