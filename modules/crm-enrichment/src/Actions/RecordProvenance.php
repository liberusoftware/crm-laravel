<?php

declare(strict_types=1);

namespace Liberu\CRM\Enrichment\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Enrichment\Models\EnrichmentField;
use Liberu\CRM\Enrichment\Models\EnrichmentProfile;
use Liberu\CRM\Enrichment\Services\EnrichmentPolicy;

final class RecordProvenance
{
    public function __construct(private readonly EnrichmentPolicy $policy) {}

    public function execute(int $teamId, int $userId, EnrichmentProfile $profile, array $input): EnrichmentField
    {
        abort_unless($profile->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['field' => ['required', 'string', 'max:120'], 'value' => ['nullable', 'string'], 'source' => ['required', 'string', 'max:120'], 'confidence' => ['nullable', 'integer', 'between:0,100']])->validate();

        return EnrichmentField::query()->create(['team_id' => $teamId, 'profile_id' => $profile->id, 'observed_at' => now(), ...$data]);
    }
}
