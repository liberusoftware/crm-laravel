<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivity\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\EmailProductivity\Models\EmailEvent;
use Liberu\CRM\EmailProductivity\Models\EmailMessage;

final class RecordEmailEvent
{
    public function execute(int $teamId, EmailMessage $message, array $input): EmailEvent
    {
        abort_unless($message->team_id === $teamId, 403);
        $data = Validator::make($input, ['event' => ['required', 'in:sent,delivered,opened,clicked,replied,bounced'], 'metadata' => ['nullable', 'array']])->validate();
        $message->update(['status' => $data['event'] === 'replied' ? 'replied' : $message->status]);

        return EmailEvent::query()->create(['team_id' => $teamId, 'message_id' => $message->id, 'occurred_at' => now(), ...$data]);
    }
}
