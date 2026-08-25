<?php

declare(strict_types=1);

namespace Liberu\CRM\Enrichment\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Enrichment\Models\EnrichmentChange;
use Liberu\CRM\Enrichment\Models\EnrichmentProfile;
use Liberu\CRM\Enrichment\Services\EnrichmentPolicy;

final class RecordChange
{
    public function __construct(private readonly EnrichmentPolicy $policy) {}

    public function execute(int $teamId, int $userId, EnrichmentProfile $profile, array $input): EnrichmentChange
    {
        abort_unless($profile->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['field' => ['required', 'string', 'max:120'], 'before' => ['nullable', 'string'], 'after' => ['nullable', 'string'], 'status' => ['nullable', 'in:detected,reviewed,accepted,dismissed']])->validate();

        return EnrichmentChange::query()->create(['team_id' => $teamId, 'profile_id' => $profile->id, 'detected_at' => now(), ...$data]);
    }
}
