<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Actions;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Liberu\CRM\ReputationManagement\Models\ReputationRequest;
use Liberu\CRM\ReputationManagement\Services\ReputationPolicy;

final class CreateReviewRequest
{
    public function __construct(private readonly ReputationPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ReputationRequest
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['customer_id' => ['required', 'integer'], 'connection_id' => ['nullable', 'integer'], 'channel' => ['required', 'in:email,sms,whatsapp'], 'token' => ['nullable', 'string', 'max:100']])->validate();

        return ReputationRequest::query()->create(['team_id' => $teamId, ...$data, 'token' => $data['token'] ?? Str::random(48), 'sent_at' => now()]);
    }
}
