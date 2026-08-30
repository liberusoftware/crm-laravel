<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\ConsentAndPreferences\Models\ConsentRecord;

final class WithdrawConsent
{
    public function execute(ConsentRecord $record): ConsentRecord
    {
        DB::transaction(function () use ($record): void {
            $record->update(['status' => 'withdrawn', 'withdrawn_at' => now()]);
        });

        return $record->refresh();
    }
}
