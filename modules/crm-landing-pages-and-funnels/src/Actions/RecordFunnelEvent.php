<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnels\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\LandingPagesAndFunnels\Models\Funnel;
use Liberu\CRM\LandingPagesAndFunnels\Models\FunnelEvent;
use Liberu\CRM\LandingPagesAndFunnels\Services\FunnelPolicy;

final class RecordFunnelEvent
{
    public function __construct(private readonly FunnelPolicy $policy) {}

    public function execute(int $teamId, int $userId, Funnel $funnel, array $input): FunnelEvent
    {
        abort_unless($funnel->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:preview,page_view,form_submit,order_click,conversion'], 'page_id' => ['nullable', 'integer'], 'visitor_key' => ['nullable', 'string', 'max:255'], 'payload' => ['nullable', 'array']])->validate();

        return FunnelEvent::query()->create(['team_id' => $teamId, 'funnel_id' => $funnel->id, ...$data]);
    }
}
