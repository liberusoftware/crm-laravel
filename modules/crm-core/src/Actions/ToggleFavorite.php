<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class ToggleFavorite
{
    public function execute(Model $record, int $teamId, int $userId): bool
    {
        return DB::transaction(function () use ($record, $teamId, $userId): bool {
            $query = DB::table('crm_core_favorites')
                ->where('team_id', $teamId)
                ->where('user_id', $userId)
                ->where('favoritable_type', $record::class)
                ->where('favoritable_id', $record->getKey());

            if ($query->exists()) {
                $query->delete();

                return false;
            }

            DB::table('crm_core_favorites')->insert([
                'team_id' => $teamId,
                'user_id' => $userId,
                'favoritable_type' => $record::class,
                'favoritable_id' => $record->getKey(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        });
    }
}
