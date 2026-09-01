<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\App\Pages;
use App\Filament\App\Pages\EditProfile;
use App\Filament\ModulePlugins;
use App\Http\Middleware\EnsureSsoWhenRequired;
use App\Http\Middleware\TeamsPermission;
use App\Listeners\CreatePersonalTeam;
use App\Listeners\SwitchTeam;
use App\Models\Team;
use App\Support\ThemeColors;
use Filament\Events\Auth\Registered;
use Filament\Events\TenantSet;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Event;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Jetstream;
use Liberu\Foundation\Localization\Http\Middleware\SetLocale;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->default()
            ->id('app')
            ->path('app')
            ->sidebarCollapsibleOnDesktop()
            // ->login([AuthenticatedSessionController::class, 'create'])
            // ->registration()
            // ->passwordReset()
            // ->emailVerification()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors(app(ThemeColors::class)->forSite())
            ->navigationGroups([
                'CRM',
                'Sales',
                'Marketing',
                'Communication',
                'Support',
                'Team',
                'Settings & integrations',
                'Account',
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn (): UrlGenerator|string => $this->shouldRegisterMenuItem()
                        ? url(EditProfile::getUrl())
                        : url($panel->getPath())),
            ])
            ->pages([
                Dashboard::class,
                EditProfile::class,
            ])
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->plugins(app(ModulePlugins::class)->forPanel('app'))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureSsoWhenRequired::class,
                TeamsPermission::class,
            ]);

        // if (Features::hasApiFeatures()) {
        //     $panel->userMenuItems([
        //         MenuItem::make()
        //             ->label('API Tokens')
        //             ->icon('heroicon-o-key')
        //             ->url(fn () => $this->shouldRegisterMenuItem()
        //                 ? url(Pages\ApiTokenManagerPage::getUrl())
        //                 : url($panel->getPath())),
        //     ]);
        // }

        if (Features::hasTeamFeatures()) {
            $panel
                ->tenant(Team::class, ownershipRelationship: 'team')
                ->tenantRegistration(Pages\CreateTeam::class)
                ->tenantProfile(Pages\EditTeam::class)
                ->userMenuItems([
                    MenuItem::make()
                        ->label('Team Settings')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->url(fn (): UrlGenerator|string => $this->shouldRegisterMenuItem()
                            ? url(Pages\EditTeam::getUrl())
                            : url($panel->getPath())),
                ]);
        }

        foreach (glob(base_path('modules/*/src/Legacy/Filament/App/Resources')) ?: [] as $path) {
            $panel->discoverResources(in: $path, for: 'App\\Filament\\App\\Resources');
        }

        foreach (glob(base_path('modules/*/src/Legacy/Filament/App/Pages')) ?: [] as $path) {
            $panel->discoverPages(in: $path, for: 'App\\Filament\\App\\Pages');
        }

        return $panel;
    }

    public function boot(): void
    {
        /**
         * Keep Jetstream routes enabled for team management features.
         */
        // Jetstream::$registersRoutes = false;

        /**
         * Listen and create personal team for new accounts.
         */
        Event::listen(
            Registered::class,
            CreatePersonalTeam::class,
        );

        /**
         * Listen and switch team if tenant was changed.
         */
        Event::listen(
            TenantSet::class,
            SwitchTeam::class,
        );
    }

    public function shouldRegisterMenuItem(): bool
    {
        // Only register tenant-scoped menu items (Profile/Team Settings) when a
        // tenant is set — otherwise their getUrl() throws on the tenant-less
        // /app/new registration route (UrlGenerationException, missing {tenant}).
        return (bool) (auth()->user()?->hasVerifiedEmail() && Filament::hasTenancy() && Filament::getTenant());
    }
}
