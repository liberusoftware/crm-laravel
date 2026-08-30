<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\Segmentation\Models\Audience;
use Liberu\CRM\Segmentation\Services\SegmentationPolicy;

final class CreateAudience
{
    public function execute(int $teamId, int $actorId, array $data): Audience
    {
        if (! app(SegmentationPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['name' => ['required', 'string', 'max:255'], 'kind' => ['required', 'in:static,dynamic'], 'conditions' => ['nullable', 'array'], 'exclusions' => ['nullable', 'array'], 'calculated_attributes' => ['nullable', 'array']])->validate();

        return Audience::query()->create(['team_id' => $teamId, 'name' => $data['name'], 'description' => $data['description'] ?? null, 'kind' => $data['kind'], 'status' => 'draft', 'conditions' => $data['conditions'] ?? [], 'exclusions' => $data['exclusions'] ?? [], 'calculated_attributes' => $data['calculated_attributes'] ?? [], 'created_by' => $actorId]);
    }
}
