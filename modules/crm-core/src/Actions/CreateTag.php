<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Liberu\CRM\Core\Models\Tag;

final class CreateTag
{
    public function execute(int $teamId, string $name): Tag
    {
        $name = trim($name);
        if ($teamId < 1 || $name === '') {
            throw new InvalidArgumentException('A team and tag name are required.');
        }

        return DB::transaction(fn (): Tag => Tag::query()->create([
            'team_id' => $teamId,
            'name' => $name,
            'slug' => Str::slug($name),
        ]));
    }
}
