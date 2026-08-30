<?php

declare(strict_types=1);

namespace Liberu\Foundation\WebhooksLivewire\Livewire;

use Livewire\Component;

final class Overview extends Component
{
    public function render(): mixed
    {
        return view('webhooks-livewire::overview');
    }
}
