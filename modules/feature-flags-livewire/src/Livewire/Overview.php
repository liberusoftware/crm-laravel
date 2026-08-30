<?php

declare(strict_types=1);

namespace Liberu\Foundation\FeatureFlagsLivewire\Livewire;

use Livewire\Component;

final class Overview extends Component
{
    public function render(): mixed
    {
        return view('feature-flags-livewire::overview');
    }
}
