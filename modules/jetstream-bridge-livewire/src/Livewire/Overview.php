<?php

declare(strict_types=1);

namespace Liberu\Foundation\JetstreamBridgeLivewire\Livewire;

use Livewire\Component;

final class Overview extends Component
{
    public function render(): mixed
    {
        return view('jetstream-bridge-livewire::overview');
    }
}
