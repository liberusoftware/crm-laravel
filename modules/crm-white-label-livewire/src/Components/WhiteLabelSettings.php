<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\WhiteLabel\Actions\UpdateWhiteLabelSettings;
use Liberu\CRM\WhiteLabel\Queries\WhiteLabelSettingsQuery;
use Livewire\Component;

final class WhiteLabelSettings extends Component
{
    public string $brandName = '';

    public string $customDomain = '';

    public string $theme = 'default';

    public string $provider = 'platform';

    public bool $showPlatformAttribution = true;

    public int $version = 1;

    public function mount(WhiteLabelSettingsQuery $query): void
    {
        $settings = $query->forTeam($this->teamId());
        $this->brandName = (string) ($settings->brand_name ?? '');
        $this->customDomain = (string) ($settings->custom_domain ?? '');
        $this->theme = (string) $settings->getAttribute('theme');
        $this->provider = (string) $settings->getAttribute('provider');
        $this->showPlatformAttribution = (bool) $settings->getAttribute('show_platform_attribution');
        $this->version = (int) $settings->getAttribute('version');
    }

    public function save(UpdateWhiteLabelSettings $update): void
    {
        $this->validate(['brandName' => ['nullable', 'string', 'max:255'], 'customDomain' => ['nullable', 'string', 'max:255'], 'theme' => ['required', 'string', 'max:100'], 'provider' => ['required', 'string', 'max:100'], 'showPlatformAttribution' => ['required', 'boolean']]);
        $settings = $update->execute($this->teamId(), (int) auth()->id(), ['brand_name' => $this->brandName ?: null, 'custom_domain' => $this->customDomain ?: null, 'theme' => $this->theme, 'provider' => $this->provider, 'show_platform_attribution' => $this->showPlatformAttribution], $this->version);
        $this->version = (int) $settings->getAttribute('version');
        $this->dispatch('white-label-settings-saved');
    }

    public function render(): View
    {
        return app('view')->make('crm-white-label-livewire::settings');
    }

    private function teamId(): int
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return (int) $id;
    }
}
