<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspace\Queries;

use Liberu\CRM\ProductWorkspace\Models\ProductEntitlement;
use Liberu\CRM\ProductWorkspace\Models\WorkspaceProduct;

final class ProductWorkspaceQuery
{
    public function products(int $teamId)
    {
        return WorkspaceProduct::query()->where('team_id', $teamId)->where('eligible', true)->orderBy('name');
    }

    public function entitlements(int $teamId, int $customerId)
    {
        return ProductEntitlement::query()->where('team_id', $teamId)->where('customer_id', $customerId)->where('status', 'active')->latest();
    }
}
