<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordination\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\FieldServiceCoordination\Models\WorkType;
use Liberu\CRM\FieldServiceCoordination\Services\FieldServicePolicy;

final class CreateWorkType
{
    public function __construct(private readonly FieldServicePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): WorkType
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:120'], 'code' => ['required', 'string', 'max:40'], 'default_duration' => ['nullable', 'integer', 'min:1'], 'requirements' => ['nullable', 'array'], 'active' => ['nullable', 'boolean']])->validate();

        return WorkType::query()->create(['team_id' => $teamId, ...$data]);
    }
}
