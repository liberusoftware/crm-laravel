<?php

declare(strict_types=1);

namespace Liberu\Foundation\IntegrationsLivewire\Livewire;

use Livewire\Component;

final class Overview extends Component
{
    public function render(): mixed
    {
        return view('integrations-livewire::overview');
    }
}
