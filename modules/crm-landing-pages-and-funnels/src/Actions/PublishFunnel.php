<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnels\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\LandingPagesAndFunnels\Models\Funnel;
use Liberu\CRM\LandingPagesAndFunnels\Services\FunnelPolicy;

final class PublishFunnel
{
    public function __construct(private readonly FunnelPolicy $policy) {}

    public function execute(int $teamId, int $userId, Funnel $funnel, array $input): Funnel
    {
        abort_unless($funnel->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['status' => ['required', 'in:published,archived'], 'preview' => ['nullable', 'boolean']])->validate();
        $funnel->update(['status' => $data['status']]);
        $funnel->pages()->where('status', 'draft')->update(['status' => $data['status'] === 'published' ? 'published' : 'archived']);

        return $funnel->refresh();
    }
}
