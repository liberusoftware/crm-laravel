<?php

declare(strict_types=1);

namespace Liberu\Foundation\SchedulerQueuesLivewire\Livewire;

use Livewire\Component;

final class Overview extends Component
{
    public function render(): mixed
    {
        return view('scheduler-queues-livewire::overview');
    }
}
