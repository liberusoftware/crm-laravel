<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\DialerAndOutreach\Models\DialerList;
use Liberu\CRM\DialerAndOutreach\Services\DialerPolicy;

final class CreateDialerList
{
    public function __construct(private readonly DialerPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): DialerList
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:160'], 'mode' => ['required', 'in:power,preview,progressive'], 'local_time_policy' => ['nullable', 'array'], 'compliance' => ['required', 'array'], 'script' => ['nullable', 'array']])->validate();

        return DialerList::query()->create(['team_id' => $teamId, 'owner_id' => $userId, ...$data]);
    }
}
