<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\RevenueLifecycle\Models\RevenueFallout;
use Liberu\CRM\RevenueLifecycle\Services\RevenuePolicy;

final class ResolveFallout
{
    public function __construct(private readonly RevenuePolicy $policy) {}

    public function execute(int $teamId, int $userId, int $falloutId, array $input): RevenueFallout
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['status' => ['required', 'in:resolved,ignored'], 'details' => ['nullable', 'array']])->validate();
        $fallout = RevenueFallout::query()->where('team_id', $teamId)->findOrFail($falloutId);
        $fallout->update($data);

        return $fallout->refresh();
    }
}
