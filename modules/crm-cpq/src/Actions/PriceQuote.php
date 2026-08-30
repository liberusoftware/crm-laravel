<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQ\Actions;

use Liberu\CRM\CPQ\Models\CpqQuote;

final class PriceQuote
{
    /** @param array{name?:string,configuration?:array<string,mixed>,lines?:array<int,array{unit_price?:float|int,quantity?:float|int,discount?:float|int}>} $input */
    public function execute(int $teamId, int $userId, array $input): CpqQuote
    {
        $lines = $input['lines'] ?? [];
        $subtotal = 0.0;
        $discount = 0.0;
        foreach ($lines as $line) {
            $amount = (float) ($line['unit_price'] ?? 0) * (float) ($line['quantity'] ?? 1);
            $subtotal += $amount;
            $discount += (float) ($line['discount'] ?? 0);
        } $total = max(0, $subtotal - $discount);
        abort_unless($total > 0, 422);

        return CpqQuote::query()->create(['team_id' => $teamId, 'owner_id' => $userId, 'name' => trim((string) ($input['name'] ?? 'Quote')), 'status' => 'draft', 'configuration' => $input['configuration'] ?? [], 'lines' => $lines, 'subtotal' => $subtotal, 'discount' => $discount, 'total' => $total, 'margin' => null]);
    }
}
