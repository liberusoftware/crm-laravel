<?php

declare(strict_types=1);

namespace Liberu\CRM\Enrichment\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Enrichment\Models\EnrichmentProfile;
use Liberu\CRM\Enrichment\Services\EnrichmentPolicy;

final class UpsertEnrichment
{
    public function __construct(private readonly EnrichmentPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): EnrichmentProfile
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['entity_type' => ['required', 'in:company,contact'], 'entity_key' => ['required', 'string', 'max:255'], 'confidence' => ['nullable', 'integer', 'between:0,100'], 'firmographic' => ['nullable', 'array'], 'demographic' => ['nullable', 'array'], 'technographic' => ['nullable', 'array'], 'social' => ['nullable', 'array'], 'verification' => ['nullable', 'array'], 'provider' => ['nullable', 'string', 'max:80']])->validate();

        return EnrichmentProfile::query()->updateOrCreate(['team_id' => $teamId, 'entity_type' => $data['entity_type'], 'entity_key' => $data['entity_key']], ['status' => 'enriched', 'enriched_at' => now(), ...$data]);
    }
}
