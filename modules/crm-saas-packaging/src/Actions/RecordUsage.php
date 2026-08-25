<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\SaasPackaging\Models\SaasUsage;
use Liberu\CRM\SaasPackaging\Services\SaasPolicy;

final class RecordUsage
{
    public function execute(int $teamId, int $actorId, array $data): SaasUsage
    {
        if (! app(SaasPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['feature' => ['required', 'string', 'max:100'], 'quantity' => ['required', 'integer', 'min:0'], 'period_start' => ['required', 'date'], 'period_end' => ['required', 'date', 'after:period_start']])->validate();
        $periodStart = Carbon::parse($data['period_start']);
        $periodEnd = Carbon::parse($data['period_end']);
        $usage = SaasUsage::query()->firstOrNew(['team_id' => $teamId, 'feature' => $data['feature'], 'period_start' => $periodStart, 'period_end' => $periodEnd]);
        $usage->quantity += (int) $data['quantity'];
        $usage->save();

        return $usage;
    }
}
