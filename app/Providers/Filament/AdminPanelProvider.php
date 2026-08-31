<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\ManageGeneralSettings;
use App\Filament\App\Pages;
use App\Filament\App\Resources\TeamRoleResource;
use App\Filament\Pages\ReportCustomizer;
use App\Filament\Resources\TeamBackupResource;
use App\Filament\Resources\TeamResource;
use App\Support\ThemeColors;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages as FilamentPage;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Jetstream\Jetstream;
use Liberu\Foundation\IdentityFilament\Resources\UserResource as FoundationUserResource;
use Liberu\Foundation\Localization\Http\Middleware\SetLocale;
use Liberu\Foundation\Organizations\Models\Team;
use Liberu\Foundation\OrganizationsFilament\Resources\TeamResource as FoundationTeamResource;
use Liberu\Foundation\SettingsFilament\SettingsFilamentPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors(app(ThemeColors::class)->forSite())
            ->navigationGroups([
                'Administration',
                'Teams',
                'Team',
                'Settings',
            ])
            ->tenantMenu(fn (): bool => Filament::getTenant() !== null)
            ->resources([
                TeamBackupResource::class,
                TeamResource::class,
                TeamRoleResource::class,
                FoundationUserResource::class,
            ])
            ->authenticatedRoutes(function (Panel $panel): void {
                TeamBackupResource::registerRoutes($panel);
                TeamResource::registerRoutes($panel);
                TeamRoleResource::registerRoutes($panel);
                FoundationTeamResource::registerRoutes($panel);
                FoundationUserResource::registerRoutes($panel);
                ReportCustomizer::registerRoutes($panel);
                ManageGeneralSettings::registerRoutes($panel);
            })
            ->tenant(Team::class, ownershipRelationship: 'team')
            ->navigation(fn (): bool => Filament::getTenant() !== null)
            ->pages([
                FilamentPage\Dashboard::class,
                Pages\EditProfile::class,
                ReportCustomizer::class,
            ])
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup('Administration'),
                SettingsFilamentPlugin::make(),
            ]);

        foreach (glob(base_path('modules/*/src/Legacy/Filament/Resources')) ?: [] as $path) {
            $panel->discoverResources(in: $path, for: 'App\\Filament\\Resources');
        }

        foreach (glob(base_path('modules/*/src/Legacy/Filament/Admin/Resources')) ?: [] as $path) {
            $panel->discoverResources(in: $path, for: 'App\\Filament\\Admin\\Resources');
        }

        foreach (glob(base_path('modules/*/src/Legacy/Filament/Admin/Pages')) ?: [] as $path) {
            $panel->discoverPages(in: $path, for: 'App\\Filament\\Admin\\Pages');
        }

        return $panel;
    }

    public function boot(): void
    {
        // Jetstream routes remain enabled for team management features.
    }
}
