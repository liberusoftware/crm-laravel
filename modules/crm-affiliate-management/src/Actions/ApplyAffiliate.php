<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\AffiliateManagement\Models\Affiliate;

final class ApplyAffiliate
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, array $input): Affiliate
    {
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:180'], 'email' => ['nullable', 'email'], 'user_id' => ['nullable', 'integer'], 'profile' => ['nullable', 'array']])->validate();

        return Affiliate::query()->create(array_merge($data, ['team_id' => $teamId, 'status' => 'applicant']));
    }
}
