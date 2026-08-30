<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Prospecting\Models\Prospect;
use Liberu\CRM\Prospecting\Services\ProspectingPolicy;

final class UpdateProspect
{
    public function __construct(private readonly ProspectingPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $prospectId, array $input): Prospect
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, [
            'provider' => ['required', 'string', 'max:100'],
            'provider_id' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'provenance' => ['required', 'array'],
            'metadata' => ['nullable', 'array'],
        ])->validate();
        $prospect = Prospect::query()->where('team_id', $teamId)->findOrFail($prospectId);
        $prospect->update($data);

        return $prospect->refresh();
    }
}
