<?php

declare(strict_types=1);

namespace Liberu\CRM\Attribution\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Attribution\Models\Conversion;
use Liberu\CRM\Attribution\Models\Touchpoint;

final class RecordConversion
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, array $input): Conversion
    {
        $data = Validator::make($input, ['visitor_key' => ['required', 'string', 'max:255'], 'conversion_key' => ['required', 'string', 'max:180'], 'model' => ['nullable', 'in:first_touch,last_touch,linear,multi_touch'], 'value' => ['nullable', 'numeric', 'min:0']])->validate();
        $touchpoints = Touchpoint::query()->where('team_id', $teamId)->where('visitor_key', $data['visitor_key'])->oldest('occurred_at')->get();
        abort_unless($touchpoints->isNotEmpty(), 422);
        $allocations = $this->allocate($touchpoints->all(), (float) ($data['value'] ?? 0), (string) ($data['model'] ?? 'multi_touch'));

        return Conversion::query()->updateOrCreate(['team_id' => $teamId, 'visitor_key' => $data['visitor_key'], 'conversion_key' => $data['conversion_key']], ['team_id' => $teamId, 'visitor_key' => $data['visitor_key'], 'conversion_key' => $data['conversion_key'], 'model' => $data['model'] ?? 'multi_touch', 'value' => $data['value'] ?? 0, 'allocations' => $allocations, 'converted_at' => $input['converted_at'] ?? Carbon::now()]);
    }

    /** @param array<int,Touchpoint> $touchpoints @return array<string,float> */
    private function allocate(array $touchpoints, float $value, string $model): array
    {
        $selected = $model === 'first_touch' ? [$touchpoints[0]] : ($model === 'last_touch' ? [$touchpoints[count($touchpoints) - 1]] : $touchpoints);
        $share = $value / count($selected);
        $allocations = [];
        foreach ($selected as $touchpoint) {
            $source = (string) $touchpoint->getAttribute('source');
            $allocations[$source] = round(($allocations[$source] ?? 0) + $share, 2);
        }

        return $allocations;
    }
}
