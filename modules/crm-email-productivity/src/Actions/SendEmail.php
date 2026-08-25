<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivity\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\EmailProductivity\Models\EmailMessage;
use Liberu\CRM\EmailProductivity\Services\EmailProductivityPolicy;

final class SendEmail
{
    public function __construct(private readonly EmailProductivityPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): EmailMessage
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['mailbox_id' => ['nullable', 'integer', 'exists:crm_email_mailboxes,id'], 'to_address' => ['required', 'email'], 'subject' => ['required', 'string', 'max:255'], 'body' => ['required', 'string'], 'thread_key' => ['nullable', 'string', 'max:255'], 'scheduled_at' => ['nullable', 'date'], 'tracking' => ['nullable', 'array']])->validate();

        return EmailMessage::query()->create(['team_id' => $teamId, 'user_id' => $userId, 'status' => isset($data['scheduled_at']) ? 'scheduled' : 'queued', ...$data]);
    }
}
