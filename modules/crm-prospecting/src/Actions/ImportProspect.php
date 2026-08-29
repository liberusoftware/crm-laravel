<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Prospecting\Models\Prospect;
use Liberu\CRM\Prospecting\Services\ProspectingPolicy;

final class ImportProspect
{
    public function __construct(private readonly ProspectingPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): Prospect
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['search_id' => ['nullable', 'integer'], 'provider' => ['required', 'string', 'max:100'], 'provider_id' => ['nullable', 'string', 'max:255'], 'name' => ['required', 'string', 'max:255'], 'company' => ['nullable', 'string', 'max:255'], 'email' => ['nullable', 'email'], 'provenance' => ['required', 'array'], 'metadata' => ['nullable', 'array']])->validate();

        return Prospect::query()->updateOrCreate(['team_id' => $teamId, 'provider' => $data['provider'], 'provider_id' => $data['provider_id'] ?? null], ['team_id' => $teamId, ...$data]);
    }
}
