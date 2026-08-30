<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Prospecting\Models\ProspectExport;
use Liberu\CRM\Prospecting\Services\ProspectingPolicy;

final class QueueExport
{
    public function __construct(private readonly ProspectingPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ProspectExport
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['format' => ['required', 'in:csv,json'], 'purpose' => ['required', 'string', 'max:255']])->validate();

        return ProspectExport::query()->create(['team_id' => $teamId, ...$data]);
    }
}
