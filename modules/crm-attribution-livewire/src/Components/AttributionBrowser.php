<?php

declare(strict_types=1);

namespace Liberu\CRM\AttributionLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Attribution\Queries\AttributionQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class AttributionBrowser extends Component
{
    use WithPagination;

    public string $visitorKey = '';

    public string $view = 'touchpoints';

    public function render(AttributionQuery $query): View
    {
        $teamId = auth()->user()?->getAttribute('current_team_id');
        abort_unless(is_numeric($teamId) && (int) $teamId > 0, 403);
        abort_unless(in_array($this->view, ['touchpoints', 'conversions'], true), 422);
        $builder = $this->view === 'touchpoints' ? $query->touchpoints((int) $teamId) : $query->conversions((int) $teamId);
        if (trim($this->visitorKey) !== '') {
            $builder->where('visitor_key', trim($this->visitorKey));
        }
        $records = $builder->paginate(25);

        return view('crm-attribution-livewire::attribution-browser', compact('records'));
    }
}
