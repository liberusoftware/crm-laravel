<?php

declare(strict_types=1);

namespace Liberu\Foundation\TwoFactorAuthenticationLivewire\Livewire;

use Livewire\Component;

final class Overview extends Component
{
    public function render(): mixed
    {
        return view('two-factor-authentication-livewire::overview');
    }
}
