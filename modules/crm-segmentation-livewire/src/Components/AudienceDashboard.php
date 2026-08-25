<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Segmentation\Queries\SegmentationQuery;
use Livewire\Component;

final class AudienceDashboard extends Component
{
    public function render(SegmentationQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-segmentation-livewire::dashboard', ['audiences' => $query->audiences((int) $id)->limit(25)->get()]);
    }
}
