<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagement\Actions;

use Liberu\CRM\BusinessProcessManagement\Models\Process;

final class CreateProcess
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, int $ownerId, array $input): Process
    {
        $name = trim((string) ($input['name'] ?? ''));
        $key = trim((string) ($input['key'] ?? ''));
        $definition = (array) ($input['definition'] ?? []);
        abort_unless($name !== '' && preg_match('/^[a-z0-9-]+$/', $key) === 1 && $definition !== [], 422);
        abort_unless(isset($definition['steps']) && is_array($definition['steps']) && $definition['steps'] !== [], 422);

        return Process::query()->create(['team_id' => $teamId, 'owner_id' => $ownerId, 'key' => $key, 'name' => $name, 'definition' => $definition, 'status' => 'draft', 'version' => (int) ($input['version'] ?? 1)]);
    }
}
