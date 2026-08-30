<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivity\Queries;

use Liberu\CRM\EmailProductivity\Models\EmailMailbox;
use Liberu\CRM\EmailProductivity\Models\EmailMessage;
use Liberu\CRM\EmailProductivity\Models\EmailTemplate;

final class EmailProductivityQuery
{
    public function mailboxes(int $teamId)
    {
        return EmailMailbox::query()->where('team_id', $teamId)->latest();
    }

    public function templates(int $teamId)
    {
        return EmailTemplate::query()->where('team_id', $teamId)->where(fn ($query) => $query->where('shared', true)->orWhere('owner_id', (int) auth()->id()))->latest();
    }

    public function messages(int $teamId)
    {
        return EmailMessage::query()->where('team_id', $teamId)->latest();
    }
}
