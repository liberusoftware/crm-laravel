<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspace\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ProductWorkspace\Models\ProductEntitlement;
use Liberu\CRM\ProductWorkspace\Services\ProductWorkspacePolicy;

final class GrantEntitlement
{
    public function __construct(private readonly ProductWorkspacePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ProductEntitlement
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['customer_id' => ['required', 'integer'], 'product_id' => ['required', 'integer', 'exists:crm_product_workspace_products,id'], 'starts_at' => ['required', 'date'], 'ends_at' => ['nullable', 'date', 'after:starts_at'], 'metadata' => ['nullable', 'array']])->validate();

        return ProductEntitlement::query()->create(['team_id' => $teamId, 'status' => 'active', ...$data]);
    }
}
