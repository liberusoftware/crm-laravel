<?php

declare(strict_types=1);

namespace Liberu\Foundation\ActivityCommentsLivewire\Livewire;

use Livewire\Component;

final class Overview extends Component
{
    public function render(): mixed
    {
        return view('activity-comments-livewire::overview');
    }
}
