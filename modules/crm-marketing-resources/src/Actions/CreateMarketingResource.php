<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingResources\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\MarketingResources\Models\MarketingResource;
use Liberu\CRM\MarketingResources\Services\MarketingResourcePolicy;

final class CreateMarketingResource
{
    public function __construct(private readonly MarketingResourcePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): MarketingResource
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['key' => ['required', 'string', 'max:255'], 'kind' => ['required', 'in:brand_kit,content_block,campaign_file,template,cms_reference,media_reference'], 'name' => ['required', 'string', 'max:255'], 'status' => ['nullable', 'in:draft,in_review,approved,rejected,archived'], 'content' => ['nullable', 'string'], 'file_reference' => ['nullable', 'string', 'max:1000'], 'cms_reference' => ['nullable', 'string', 'max:1000'], 'metadata' => ['nullable', 'array']])->validate();

        return MarketingResource::query()->create(['team_id' => $teamId, 'owner_id' => $userId, ...$data]);
    }
}
