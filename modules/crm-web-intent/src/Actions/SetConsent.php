<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\WebIntent\Models\WebIntentConsent;
use Liberu\CRM\WebIntent\Models\WebIntentVisit;

final class SetConsent
{
    public function execute(int $teamId, string $visitorKey, string $purpose, string $status, ?string $policyVersion = null): WebIntentConsent
    {
        validator(compact('visitorKey', 'purpose', 'status'), ['visitorKey' => ['required', 'string', 'max:128'], 'purpose' => ['required', 'string', 'max:100'], 'status' => ['required', 'in:granted,denied,withdrawn']])->validate();

        return DB::transaction(function () use ($teamId, $visitorKey, $purpose, $status, $policyVersion): WebIntentConsent {
            $consent = WebIntentConsent::query()->updateOrCreate(['team_id' => $teamId, 'visitor_key' => $visitorKey, 'purpose' => $purpose], ['status' => $status, 'policy_version' => $policyVersion, 'granted_at' => $status === 'granted' ? now() : null, 'revoked_at' => $status !== 'granted' ? now() : null]);
            WebIntentVisit::query()->where('team_id', $teamId)->where('visitor_key', $visitorKey)->update(['consent_status' => $status]);

            return $consent;
        });
    }
}
