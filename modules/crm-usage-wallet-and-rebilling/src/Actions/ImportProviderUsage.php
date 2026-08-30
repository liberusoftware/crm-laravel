<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\UsageWalletAndRebilling\Models\UsageImport;
use Liberu\CRM\UsageWalletAndRebilling\Services\UsageAudit;
use Liberu\CRM\UsageWalletAndRebilling\Services\UsagePolicy;

final class ImportProviderUsage
{
    public function execute(int $teamId, int $actorId, array $data): UsageImport
    {
        if (! app(UsagePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }
        $data = validator($data, ['provider' => ['required', 'string', 'max:100'], 'external_id' => ['required', 'string', 'max:255'], 'amount' => ['required', 'numeric', 'min:0'], 'currency' => ['required', 'regex:/^[A-Z]{3}$/'], 'payload' => ['nullable', 'array']])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $data) {
            $import = UsageImport::query()->firstOrCreate(['team_id' => $teamId, 'provider' => $data['provider'], 'external_id' => $data['external_id']], array_merge($data, ['status' => 'imported']));
            app(UsageAudit::class)->record($teamId, $actorId, 'provider_usage_imported', ['import_id' => $import->id, 'provider' => $import->getAttribute('provider')]);

            return $import;
        });
    }
}
