<?php

declare(strict_types=1);

namespace Liberu\CRM\Documents\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Documents\Models\CrmDocument;
use Liberu\CRM\Documents\Services\DocumentsPolicy;

final class CreateDocument
{
    public function __construct(private readonly DocumentsPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): CrmDocument
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:180'], 'kind' => ['nullable', 'in:file,template'], 'folder' => ['nullable', 'string', 'max:160'], 'storage_provider' => ['nullable', 'in:local,s3,drive,dropbox'], 'storage_key' => ['required', 'string', 'max:255'], 'retention_until' => ['nullable', 'date'], 'access' => ['nullable', 'array']])->validate();

        return CrmDocument::query()->create(['team_id' => $teamId, 'owner_id' => $userId, ...$data]);
    }
}
