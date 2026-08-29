<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\AffiliateManagement\Models\AffiliateEvent;
use Liberu\CRM\AffiliateManagement\Models\AffiliateLink;

final class RecordAffiliateEvent
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, AffiliateLink $link, array $input): AffiliateEvent
    {
        abort_unless((int) $link->team_id === $teamId && $link->status === 'active', 404);
        $data = Validator::make($input, ['type' => ['required', 'in:click,conversion'], 'external_key' => ['nullable', 'string', 'max:180'], 'value' => ['nullable', 'numeric', 'min:0'], 'commission_rate' => ['nullable', 'numeric', 'between:0,1'], 'metadata' => ['nullable', 'array']])->validate();
        $value = (float) ($data['value'] ?? 0);
        $rate = (float) ($data['commission_rate'] ?? 0);
        unset($data['commission_rate']);

        return AffiliateEvent::query()->create(array_merge($data, ['team_id' => $teamId, 'affiliate_id' => $link->affiliate_id, 'link_id' => $link->getKey(), 'commission' => round($value * $rate, 2), 'status' => $data['type'] === 'conversion' ? 'pending' : 'recorded']));
    }
}
