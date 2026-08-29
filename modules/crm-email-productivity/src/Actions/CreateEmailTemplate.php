<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivity\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\EmailProductivity\Models\EmailTemplate;
use Liberu\CRM\EmailProductivity\Services\EmailProductivityPolicy;

final class CreateEmailTemplate
{
    public function __construct(private readonly EmailProductivityPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): EmailTemplate
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:120'], 'kind' => ['nullable', 'in:template,snippet,signature'], 'subject' => ['required', 'string', 'max:255'], 'body' => ['required', 'string'], 'shared' => ['nullable', 'boolean']])->validate();

        return EmailTemplate::query()->create(['team_id' => $teamId, 'owner_id' => $userId, ...$data]);
    }
}
