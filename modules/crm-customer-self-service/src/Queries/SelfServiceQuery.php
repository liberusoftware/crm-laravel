<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfService\Queries;

use Liberu\CRM\CustomerSelfService\Models\SelfServiceCase;
use Liberu\CRM\CustomerSelfService\Models\SelfServiceProfile;
use Liberu\CRM\CustomerSelfService\Models\SelfServiceResource;

final class SelfServiceQuery
{
    public function profile(int $teamId, int $userId): ?SelfServiceProfile
    {
        return SelfServiceProfile::query()->where('team_id', $teamId)->where('user_id', $userId)->first();
    }

    public function cases(int $teamId, int $profileId)
    {
        return SelfServiceCase::query()->where('team_id', $teamId)->where('profile_id', $profileId)->latest();
    }

    public function search(int $teamId, string $term)
    {
        return SelfServiceResource::query()->where('team_id', $teamId)->where('published', true)->where(fn ($query) => $query->where('title', 'like', '%'.$term.'%')->orWhere('content', 'like', '%'.$term.'%'))->latest();
    }
}
