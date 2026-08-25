<?php

declare(strict_types=1);

namespace Liberu\CRM\Contracts\Actions;

use Liberu\CRM\Contracts\Models\Contract;

final class CreateContract
{
    /** @param array{name?:string,parties?:array<string,mixed>,terms?:array<string,mixed>,clauses?:array<string,mixed>,obligations?:array<string,mixed>,starts_on?:string,ends_on?:string,renewal_on?:string} $input */
    public function execute(int $teamId, int $userId, array $input): Contract
    {
        $name = trim((string) ($input['name'] ?? ''));
        abort_unless($name !== '' && ($input['parties'] ?? []) !== [] && ($input['terms'] ?? []) !== [], 422);

        return Contract::query()->create(['team_id' => $teamId, 'owner_id' => $userId, 'name' => $name, 'status' => 'draft', 'version' => 1, 'parties' => $input['parties'], 'terms' => $input['terms'], 'clauses' => $input['clauses'] ?? [], 'obligations' => $input['obligations'] ?? [], 'repository_links' => [], 'starts_on' => $input['starts_on'] ?? null, 'ends_on' => $input['ends_on'] ?? null, 'renewal_on' => $input['renewal_on'] ?? null]);
    }
}
