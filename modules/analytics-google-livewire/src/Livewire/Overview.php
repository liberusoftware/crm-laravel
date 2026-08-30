<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsGoogleLivewire\Livewire;

use Livewire\Component;

final class Overview extends Component
{
    public function render(): mixed
    {
        return view('analytics-google-livewire::overview');
    }
}
