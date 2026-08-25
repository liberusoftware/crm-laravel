<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnels\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\LandingPagesAndFunnels\Models\Funnel;
use Liberu\CRM\LandingPagesAndFunnels\Models\FunnelPage;
use Liberu\CRM\LandingPagesAndFunnels\Services\FunnelPolicy;

final class AddFunnelPage
{
    public function __construct(private readonly FunnelPolicy $policy) {}

    public function execute(int $teamId, int $userId, Funnel $funnel, array $input): FunnelPage
    {
        abort_unless($funnel->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['slug' => ['required', 'string', 'max:255'], 'kind' => ['required', 'in:landing,thank_you,template'], 'position' => ['required', 'integer', 'min:0'], 'content' => ['nullable', 'string'], 'seo' => ['nullable', 'array'], 'personalization' => ['nullable', 'array'], 'form' => ['nullable', 'array'], 'order_link' => ['nullable', 'string', 'max:1000']])->validate();

        return FunnelPage::query()->create(['team_id' => $teamId, 'funnel_id' => $funnel->id, ...$data]);
    }
}
