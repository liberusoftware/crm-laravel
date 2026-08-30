<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\DialerAndOutreach\Models\DialerCall;
use Liberu\CRM\DialerAndOutreach\Models\DialerList;
use Liberu\CRM\DialerAndOutreach\Services\DialerPolicy;

final class QueueDialerCall
{
    public function __construct(private readonly DialerPolicy $policy) {}

    public function execute(int $teamId, int $userId, DialerList $list, array $input): DialerCall
    {
        abort_unless($list->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        abort_unless(($list->compliance['consent_required'] ?? false) === false || (bool) ($input['consent'] ?? false), 422, 'Consent is required.');
        $data = Validator::make($input, ['phone' => ['required', 'string', 'max:40'], 'contact_id' => ['nullable', 'integer'], 'consent' => ['nullable', 'boolean'], 'next_attempt_at' => ['nullable', 'date']])->validate();

        unset($data['consent']);

        return DialerCall::query()->create(['team_id' => $teamId, 'list_id' => $list->id, 'status' => 'queued', ...$data]);
    }
}
