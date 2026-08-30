<?php

declare(strict_types=1);

namespace Liberu\Foundation\ApiAccessLivewire\Livewire;

use Livewire\Component;

final class Overview extends Component
{
    public function render(): mixed
    {
        return view('api-access-livewire::overview');
    }
}
