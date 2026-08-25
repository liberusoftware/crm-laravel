<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistration\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Liberu\CRM\DealRegistration\Models\DealRegistration;
use Liberu\CRM\DealRegistration\Models\DealRegistrationEvent;
use Liberu\CRM\DealRegistration\Services\DealRegistrationPolicy;

final class SubmitDeal
{
    public function __construct(private readonly DealRegistrationPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): DealRegistration
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['external_key' => ['required', 'string', 'max:120'], 'company' => ['required', 'string', 'max:180'], 'contact_email' => ['required', 'email'], 'partner_id' => ['nullable', 'integer'], 'territory' => ['nullable', 'string', 'max:80'], 'description' => ['nullable', 'string'], 'attribution' => ['nullable', 'array']])->validate();

        return DB::transaction(function () use ($teamId, $userId, $data): DealRegistration {
            $duplicate = DealRegistration::query()->where('team_id', $teamId)->where('contact_email', $data['contact_email'])->whereIn('status', ['pending', 'approved', 'protected'])->exists();
            abort_if($duplicate, 409, 'A deal for this contact is already registered.');
            $deal = DealRegistration::query()->create(['team_id' => $teamId, 'owner_id' => $userId, 'status' => 'pending', ...$data]);
            DealRegistrationEvent::query()->create(['team_id' => $teamId, 'deal_id' => $deal->id, 'actor_id' => $userId, 'event' => 'submitted', 'occurred_at' => now()]);

            return $deal;
        });
    }
}
