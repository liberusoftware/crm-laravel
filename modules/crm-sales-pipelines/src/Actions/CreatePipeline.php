<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesPipelines\Models\SalesPipeline;
use Liberu\CRM\SalesPipelines\Services\PipelinePolicy;

final class CreatePipeline
{
    public function execute(int $teamId, int $actorId, array $data): SalesPipeline
    {
        if (! app(PipelinePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['name' => ['required', 'string', 'max:255']])->validate();

        return SalesPipeline::query()->create(['team_id' => $teamId, 'name' => $data['name'], 'active' => true]);
    }
}
