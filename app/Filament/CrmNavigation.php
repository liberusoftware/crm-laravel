<?php

declare(strict_types=1);

namespace App\Filament;

use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\Resources\Resource;

final class CrmNavigation
{
    public function configure(Panel $panel): void
    {
        $panel->navigationGroups([
            NavigationGroup::make('CRM')->icon('heroicon-o-squares-2x2')->collapsed(),
            NavigationGroup::make('Sales')->icon('heroicon-o-chart-bar')->collapsed(),
            NavigationGroup::make('Marketing')->icon('heroicon-o-megaphone')->collapsed(),
            NavigationGroup::make('Communication')->icon('heroicon-o-chat-bubble-left-right')->collapsed(),
            NavigationGroup::make('Support')->icon('heroicon-o-lifebuoy')->collapsed(),
            NavigationGroup::make('Operations')->icon('heroicon-o-cog-6-tooth')->collapsed(),
            NavigationGroup::make('Data & intelligence')->icon('heroicon-o-light-bulb')->collapsed(),
            NavigationGroup::make('Team')->icon('heroicon-o-user-group')->collapsed(),
            NavigationGroup::make('Settings & integrations')->icon('heroicon-o-wrench-screwdriver')->collapsed(),
            NavigationGroup::make('Account')->icon('heroicon-o-user-circle')->collapsed(),
        ])->collapsibleNavigationGroups();

        foreach ($panel->getResources() as $resource) {
            if (! is_a($resource, Resource::class, true)) {
                continue;
            }

            if ($resource::getNavigationGroup() !== null) {
                continue;
            }

            $resource::navigationGroup($this->groupFor($resource));
        }
    }

    private function groupFor(string $resource): string
    {
        $resource = strtolower($resource);

        return match (true) {
            str_contains($resource, 'sales'),
            str_contains($resource, 'account'),
            str_contains($resource, 'affiliate'),
            str_contains($resource, 'attribution'),
            str_contains($resource, 'deal'),
            str_contains($resource, 'forecast'),
            str_contains($resource, 'leadqualification'),
            str_contains($resource, 'pipeline'),
            str_contains($resource, 'prospect'),
            str_contains($resource, 'quote'),
            str_contains($resource, 'revenue') => 'Sales',

            str_contains($resource, 'marketing'),
            str_contains($resource, 'advertis'),
            str_contains($resource, 'campaign'),
            str_contains($resource, 'enrichment'),
            str_contains($resource, 'funnel'),
            str_contains($resource, 'journey'),
            str_contains($resource, 'webintent') => 'Marketing',

            str_contains($resource, 'activit'),
            str_contains($resource, 'chat'),
            str_contains($resource, 'conversation'),
            str_contains($resource, 'dialer'),
            str_contains($resource, 'email'),
            str_contains($resource, 'messag'),
            str_contains($resource, 'telephon') => 'Communication',

            str_contains($resource, 'customer'),
            str_contains($resource, 'case'),
            str_contains($resource, 'contactcenter'),
            str_contains($resource, 'knowledge'),
            str_contains($resource, 'onboarding'),
            str_contains($resource, 'service'),
            str_contains($resource, 'support'),
            str_contains($resource, 'ticket') => 'Support',

            str_contains($resource, 'analytic'),
            str_contains($resource, 'copilot'),
            str_contains($resource, 'data'),
            str_contains($resource, 'intelligen'),
            str_contains($resource, 'predict'),
            str_contains($resource, 'search') => 'Data & intelligence',

            str_contains($resource, 'automation'),
            str_contains($resource, 'document'),
            str_contains($resource, 'process'),
            str_contains($resource, 'project'),
            str_contains($resource, 'work'),
            str_contains($resource, 'planning'),
            str_contains($resource, 'resource') => 'Operations',

            default => 'CRM',
        };
    }
}
