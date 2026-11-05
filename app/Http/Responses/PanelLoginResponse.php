<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Filament\Admin\Pages\SetupWizard;
use App\Models\Team;
use App\Models\User;
use App\Services\Marketing\PlatformConfiguration;
use App\Services\Marketing\WorkspaceAccess;
use Filament\Auth\Http\Responses\LoginResponse;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

final class PanelLoginResponse extends LoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = Filament::auth()->user();
        $panel = Filament::getCurrentPanel()?->getId();
        $team = $user instanceof User ? $user->currentTeam : null;
        if ($user instanceof User && $team) {
            if ($panel === 'admin' && $user->isSuperAdmin() && ! app(PlatformConfiguration::class)->settings()?->getAttribute('setup_completed_at')) {
                return redirect(SetupWizard::getUrl(['tenant' => $team], panel: 'admin'));
            }
            if ($panel === 'app' && $team instanceof Team && ! $team->getAttribute('setup_completed_at') && app(WorkspaceAccess::class)->canManage($user, $team)) {
                return redirect(\App\Filament\App\Pages\SetupWizard::getUrl(['tenant' => $team], panel: 'app'));
            }
        }

        return parent::toResponse($request);
    }
}
