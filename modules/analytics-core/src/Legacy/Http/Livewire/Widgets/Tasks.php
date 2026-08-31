<?php

declare(strict_types=1);

namespace App\Http\Livewire\Widgets;

use App\Models\DashboardWidget;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Tasks extends Component
{
    public DashboardWidget $widget;

    public function render(): Factory|View
    {
        return view('livewire.widgets.tasks');
    }
}
