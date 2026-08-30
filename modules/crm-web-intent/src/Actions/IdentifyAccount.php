<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\WebIntent\Models\WebIntentIdentification;
use Liberu\CRM\WebIntent\Services\WebIntentAudit;
use Liberu\CRM\WebIntent\Services\WebIntentPolicy;

final class IdentifyAccount
{
    /** @param array<string, mixed> $metadata */
    public function execute(int $teamId, int $actorId, string $visitorKey, string $adapter, ?string $accountName, ?string $accountDomain, int $confidence, array $metadata, WebIntentPolicy $policy): WebIntentIdentification
    {
        abort_unless($policy->canManage($teamId, $actorId), 403, 'You cannot manage web-intent identifications for this team.');
        validator(compact('visitorKey', 'adapter', 'confidence'), ['visitorKey' => ['required', 'string', 'max:128'], 'adapter' => ['required', 'string', 'max:100'], 'confidence' => ['required', 'integer', 'between:0,100']])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $visitorKey, $adapter, $accountName, $accountDomain, $confidence, $metadata): WebIntentIdentification {
            $identification = WebIntentIdentification::query()->updateOrCreate(['team_id' => $teamId, 'visitor_key' => $visitorKey, 'adapter' => $adapter], ['account_name' => $accountName, 'account_domain' => $accountDomain, 'confidence' => $confidence, 'status' => $confidence >= 70 ? 'identified' : 'pending', 'metadata' => $metadata]);
            app(WebIntentAudit::class)->record($teamId, $actorId, 'account_identified', ['visitor_key' => $visitorKey, 'adapter' => $adapter]);

            return $identification;
        });
    }
}
