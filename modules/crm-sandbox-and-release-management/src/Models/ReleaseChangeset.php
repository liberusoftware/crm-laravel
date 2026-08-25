<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $status
 * @property array<string,mixed> $changes
 * @property array<string,mixed>|null $validation
 * @property string $target_environment
 */
final class ReleaseChangeset extends Model
{
    protected $table = 'crm_release_changesets';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['changes' => 'array', 'dependencies' => 'array', 'validation' => 'array'];
    }
}
