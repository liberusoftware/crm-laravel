<?php

declare(strict_types=1);

namespace Liberu\CRM\Loyalty\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Loyalty\Models\LoyaltyLedgerEntry;
use Liberu\CRM\Loyalty\Models\LoyaltyMember;
use Liberu\CRM\Loyalty\Services\LoyaltyPolicy;

final class RecordPoints
{
    public function __construct(private readonly LoyaltyPolicy $policy) {}

    public function execute(int $teamId, int $userId, LoyaltyMember $member, array $input): LoyaltyLedgerEntry
    {
        abort_unless($member->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:earn,redeem,expire,partner_activity'], 'points' => ['required', 'numeric', 'not_in:0'], 'reference' => ['required', 'string', 'max:255'], 'expires_on' => ['nullable', 'date'], 'metadata' => ['nullable', 'array']])->validate();
        abort_if($member->fraud_locked, 422, 'Member is fraud locked.');
        if (in_array($data['kind'], ['redeem', 'expire'], true) && $member->balance + (float) $data['points'] < 0) {
            abort(422, 'Insufficient loyalty points.');
        }

        return DB::transaction(function () use ($member, $teamId, $data): LoyaltyLedgerEntry {
            $entry = LoyaltyLedgerEntry::query()->create(['team_id' => $teamId, 'member_id' => $member->id, ...$data]);
            $member->increment('balance', (float) $data['points']);
            if ($data['kind'] === 'earn') {
                $member->increment('lifetime_earned', (float) $data['points']);
            }

            return $entry;
        });
    }
}
