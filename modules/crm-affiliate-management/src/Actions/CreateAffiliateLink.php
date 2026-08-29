<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\AffiliateManagement\Models\Affiliate;
use Liberu\CRM\AffiliateManagement\Models\AffiliateLink;

final class CreateAffiliateLink
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, Affiliate $affiliate, array $input): AffiliateLink
    {
        abort_unless((int) $affiliate->team_id === $teamId && $affiliate->status === 'active', 422);
        $data = Validator::make($input, ['code' => ['required', 'alpha_dash', 'max:80'], 'destination' => ['required', 'url', 'max:500'], 'campaign' => ['nullable', 'string', 'max:180']])->validate();

        return AffiliateLink::query()->create(array_merge($data, ['team_id' => $teamId, 'affiliate_id' => $affiliate->getKey(), 'status' => 'active']));
    }
}
