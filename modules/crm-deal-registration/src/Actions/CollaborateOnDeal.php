<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistration\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\DealRegistration\Models\DealRegistration;
use Liberu\CRM\DealRegistration\Services\DealRegistrationPolicy;

final class CollaborateOnDeal
{
    public function __construct(private readonly DealRegistrationPolicy $policy) {}

    public function execute(int $teamId, int $userId, DealRegistration $deal, array $input): DealRegistration
    {
        abort_unless($deal->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['collaborator_id' => ['required', 'integer'], 'role' => ['required', 'string', 'max:60']])->validate();
        $deal->update(['collaborators' => [...(array) $deal->collaborators, $data]]);

        return $deal->refresh();
    }
}
