<?php

namespace Liberu\Foundation\Tenancy;

use Filament\Facades\Filament;
use Throwable;

class TenantContext
{
    public static function set(?int $teamId): void
    {
        app(TenantState::class)->teamId = $teamId;
    }

    public static function clear(): void
    {
        app(TenantState::class)->teamId = null;
    }

    public static function currentId(): ?int
    {
        if (app(TenantState::class)->teamId !== null) {
            return app(TenantState::class)->teamId;
        }

        try {
            $tenant = Filament::getTenant();
        } catch (Throwable) {
            return null;
        }

        return $tenant ? (int) $tenant->getKey() : null;
    }
}
