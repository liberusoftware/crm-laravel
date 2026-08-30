<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\WebIntent\Models\WebIntentAlert;
use Liberu\CRM\WebIntent\Services\WebIntentPolicy;

final class ResolveAlert
{
    public function execute(WebIntentAlert $alert, int $actorId, WebIntentPolicy $policy): WebIntentAlert
    {
        abort_unless($policy->canManage((int) $alert->team_id, $actorId), 403, 'You cannot resolve this alert.');

        return DB::transaction(function () use ($alert, $actorId): WebIntentAlert {
            $alert->update(['status' => 'resolved', 'resolved_at' => now(), 'resolved_by' => $actorId]);

            return $alert->fresh();
        });
    }
}
