<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessaging\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\MobileMessaging\Models\MessagingContact;
use Liberu\CRM\MobileMessaging\Services\MobileMessagingPolicy;

final class UpsertContactConsent
{
    public function __construct(private readonly MobileMessagingPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): MessagingContact
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['address' => ['required', 'string', 'max:255'], 'channel' => ['required', 'in:sms,mms,whatsapp,push'], 'consent' => ['required', 'in:opted_in,opted_out,unknown'], 'keyword' => ['nullable', 'string', 'max:64'], 'metadata' => ['nullable', 'array']])->validate();

        return MessagingContact::query()->updateOrCreate(['team_id' => $teamId, 'address' => $data['address'], 'channel' => $data['channel']], ['consent' => $data['consent'], 'consent_at' => now(), 'keyword' => $data['keyword'] ?? null, 'metadata' => $data['metadata'] ?? null]);
    }
}
