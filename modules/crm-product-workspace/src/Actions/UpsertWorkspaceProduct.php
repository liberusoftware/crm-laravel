<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspace\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ProductWorkspace\Models\WorkspaceProduct;
use Liberu\CRM\ProductWorkspace\Services\ProductWorkspacePolicy;

final class UpsertWorkspaceProduct
{
    public function __construct(private readonly ProductWorkspacePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): WorkspaceProduct
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['sku' => ['required', 'string', 'max:80'], 'name' => ['required', 'string', 'max:160'], 'description' => ['nullable', 'string'], 'price' => ['nullable', 'numeric', 'min:0'], 'currency' => ['nullable', 'string', 'size:3'], 'eligible' => ['nullable', 'boolean'], 'price_book' => ['nullable', 'array'], 'metadata' => ['nullable', 'array']])->validate();

        return WorkspaceProduct::query()->updateOrCreate(['team_id' => $teamId, 'sku' => $data['sku']], $data + ['team_id' => $teamId]);
    }
}
