<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\Activities\Models\Activity;

final class CancelActivity
{
    public function execute(Activity $activity): Activity
    {
        DB::transaction(fn (): bool => $activity->update(['status' => 'cancelled', 'updated_at' => now()]));

        return $activity->refresh();
    }
}
