<?php

namespace Liberu\Foundation\Organizations\Support;

final class MembershipRolePolicy
{
    private const NEVER_ASSIGNABLE = ['customer', 'super_admin'];

    public function allows(string $role): bool
    {
        $role = $this->normalize($role);
        $roles = config('organizations-teams.assignable_roles', []);

        return $role !== null && ! in_array($role, self::NEVER_ASSIGNABLE, true) && in_array($role, $roles, true);
    }

    public function normalize(string $role): ?string
    {
        $role = mb_strtolower(trim($role));

        return $role === '' ? null : $role;
    }
}
