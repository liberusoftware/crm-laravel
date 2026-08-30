<?php

namespace Liberu\Foundation\Organizations\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Liberu\Foundation\Organizations\Support\MembershipRolePolicy;

final class InviteMember
{
    public function handle(int $teamId, string $email, string $role, int|string $inviterId, ?\DateTimeImmutable $expiresAt = null): string
    {
        $email = mb_strtolower(trim($email));
        $rolePolicy = new MembershipRolePolicy();
        $role = $rolePolicy->normalize($role);
        if ($role === null || ! $rolePolicy->allows($role)) {
            throw new InvalidArgumentException('The role cannot be assigned to a team member.');
        }

        $token = Str::random(64);
        DB::transaction(function () use ($teamId, $email, $role, $inviterId, $expiresAt, $token): void {
            DB::table('team_invitations')->updateOrInsert(['team_id' => $teamId, 'email' => $email], ['role' => $role, 'token_hash' => hash('sha256', $token), 'invited_by' => $inviterId, 'expires_at' => $expiresAt ?? now()->addDays(7), 'accepted_at' => null, 'revoked_at' => null, 'created_at' => now(), 'updated_at' => now()]);
        });

        return $token;
    }
}
