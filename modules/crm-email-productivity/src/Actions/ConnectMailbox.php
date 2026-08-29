<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivity\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\EmailProductivity\Models\EmailMailbox;
use Liberu\CRM\EmailProductivity\Services\EmailProductivityPolicy;

final class ConnectMailbox
{
    public function __construct(private readonly EmailProductivityPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): EmailMailbox
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['provider' => ['required', 'in:gmail,outlook'], 'address' => ['required', 'email'], 'credential_reference' => ['nullable', 'string', 'max:255']])->validate();

        return EmailMailbox::query()->updateOrCreate(['team_id' => $teamId, 'address' => $data['address']], ['user_id' => $userId, 'status' => 'connected', ...$data]);
    }
}
