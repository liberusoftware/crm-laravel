<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQ\Actions;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Liberu\CRM\CPQ\Models\CpqQuote;

final class PriceQuote
{
    /** @param array{name?:string,configuration?:array<string,mixed>,lines?:array<int,array{unit_price?:float|int,quantity?:float|int,discount?:float|int}>} $input */
    public function execute(int $teamId, int $userId, array $input): CpqQuote
    {
        if ($teamId < 1 || $userId < 1) {
            throw new InvalidArgumentException('A valid team and actor are required.');
        }

        $lines = $input['lines'] ?? [];
        if (! is_array($lines) || $lines === []) {
            throw new InvalidArgumentException('At least one quote line is required.');
        }

        $subtotal = 0.0;
        $discount = 0.0;
        foreach ($lines as $line) {
            if (! is_array($line) || (float) ($line['unit_price'] ?? 0) < 0 || (float) ($line['quantity'] ?? 0) <= 0 || (float) ($line['discount'] ?? 0) < 0) {
                throw new InvalidArgumentException('Quote lines must contain valid non-negative prices and discounts with a positive quantity.');
            }
            $amount = (float) ($line['unit_price'] ?? 0) * (float) ($line['quantity'] ?? 1);
            $subtotal += $amount;
            $discount += (float) ($line['discount'] ?? 0);
        }
        $total = max(0, $subtotal - $discount);
        abort_unless($total > 0, 422);

        return DB::transaction(fn (): CpqQuote => CpqQuote::query()->create(['team_id' => $teamId, 'owner_id' => $userId, 'name' => trim((string) ($input['name'] ?? 'Quote')) ?: 'Quote', 'status' => 'draft', 'currency' => strtoupper(substr((string) ($input['currency'] ?? 'USD'), 0, 3)), 'configuration' => is_array($input['configuration'] ?? null) ? $input['configuration'] : [], 'lines' => $lines, 'subtotal' => $subtotal, 'discount' => $discount, 'total' => $total, 'margin' => null]));
    }
}
