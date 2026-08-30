<?php

declare(strict_types=1);

namespace Liberu\Foundation\ModuleManagerLivewire\Livewire;

use Livewire\Component;

final class Overview extends Component
{
    public function render(): mixed
    {
        return view('module-manager-livewire::overview');
    }
}
