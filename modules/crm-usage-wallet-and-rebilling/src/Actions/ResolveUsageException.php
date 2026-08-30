<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\UsageWalletAndRebilling\Models\UsageAudit;
use Liberu\CRM\UsageWalletAndRebilling\Services\UsagePolicy;

final class ResolveUsageException
{
    public function execute(int $teamId, int $actorId, int $exceptionId): void
    {
        if (! app(UsagePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        } $row = \DB::table('crm_usage_exceptions')->where('team_id', $teamId)->where('id', $exceptionId)->first();
        if (! $row) {
            throw ValidationException::withMessages(['exception' => 'Exception not found.']);
        } \DB::table('crm_usage_exceptions')->where('id', $exceptionId)->update(['status' => 'resolved', 'resolved_by' => $actorId, 'resolved_at' => now(), 'updated_at' => now()]);
        (new UsageAudit())->create(['team_id' => $teamId, 'actor_id' => $actorId, 'event' => 'usage_exception_resolved', 'details' => ['exception_id' => $exceptionId]]);
    }
}
