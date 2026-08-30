<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsMetaLivewire\Livewire;

use Livewire\Component;

final class Overview extends Component
{
    public function render(): mixed
    {
        return view('analytics-meta-livewire::overview');
    }
}
