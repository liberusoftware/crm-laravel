<?php

declare(strict_types=1);

namespace Liberu\CRM\Attribution\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Attribution\Models\Touchpoint;

final class RecordTouchpoint
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, array $input): Touchpoint
    {
        $data = Validator::make($input, ['visitor_key' => ['required', 'string', 'max:255'], 'source' => ['required', 'string', 'max:120'], 'medium' => ['nullable', 'string', 'max:120'], 'campaign' => ['nullable', 'string', 'max:180'], 'content' => ['nullable', 'string', 'max:180'], 'term' => ['nullable', 'string', 'max:180'], 'click_id' => ['nullable', 'string', 'max:255'], 'channel' => ['nullable', 'string', 'max:80'], 'cost' => ['nullable', 'numeric', 'min:0'], 'metadata' => ['nullable', 'array']])->validate();
        $data['source'] = strtolower(trim((string) $data['source']));
        $data['occurred_at'] = $input['occurred_at'] ?? Carbon::now();

        return Touchpoint::query()->updateOrCreate(['team_id' => $teamId, 'visitor_key' => $data['visitor_key'], 'click_id' => $data['click_id'] ?? null], array_merge($data, ['team_id' => $teamId]));
    }
}
