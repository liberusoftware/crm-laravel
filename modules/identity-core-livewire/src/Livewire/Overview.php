<?php

declare(strict_types=1);

namespace Liberu\Foundation\IdentityCoreLivewire\Livewire;

use Livewire\Component;

final class Overview extends Component
{
    public function render(): mixed
    {
        return view('identity-core-livewire::overview');
    }
}
