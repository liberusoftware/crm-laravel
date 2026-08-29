<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesPipelines\Models\SalesPipeline;
use Liberu\CRM\SalesPipelines\Models\SalesStage;
use Liberu\CRM\SalesPipelines\Services\PipelinePolicy;

final class CreateStage
{
    public function execute(int $teamId, int $actorId, array $data): SalesStage
    {
        if (! app(PipelinePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['pipeline_id' => ['required', 'integer'], 'name' => ['required', 'string', 'max:255'], 'position' => ['required', 'integer', 'min:0'], 'probability' => ['nullable', 'numeric', 'between:0,100'], 'rotting_days' => ['nullable', 'integer', 'min:1']])->validate();
        if (! SalesPipeline::query()->where('team_id', $teamId)->whereKey($data['pipeline_id'])->exists()) {
            throw ValidationException::withMessages(['pipeline_id' => 'Pipeline does not belong to this team.']);
        }

        return SalesStage::query()->create(array_merge($data, ['probability' => $data['probability'] ?? 0]));
    }
}
