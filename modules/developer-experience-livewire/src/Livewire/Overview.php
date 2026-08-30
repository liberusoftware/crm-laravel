<?php

declare(strict_types=1);

namespace Liberu\Foundation\DeveloperExperienceLivewire\Livewire;

use Livewire\Component;

final class Overview extends Component
{
    public function render(): mixed
    {
        return view('developer-experience-livewire::overview');
    }
}
