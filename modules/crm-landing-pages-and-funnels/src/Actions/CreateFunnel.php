<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnels\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\LandingPagesAndFunnels\Models\Funnel;
use Liberu\CRM\LandingPagesAndFunnels\Services\FunnelPolicy;

final class CreateFunnel
{
    public function __construct(private readonly FunnelPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): Funnel
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['slug' => ['required', 'string', 'max:255'], 'name' => ['required', 'string', 'max:255'], 'domain' => ['nullable', 'string', 'max:255'], 'metadata' => ['nullable', 'array']])->validate();

        return Funnel::query()->create(['team_id' => $teamId, ...$data]);
    }
}
