<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagementLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\AffiliateManagement\Actions\ApproveAffiliate;
use Liberu\CRM\AffiliateManagement\Models\Affiliate;
use Liberu\CRM\AffiliateManagement\Queries\AffiliateQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class AffiliateBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public function approve(int $affiliateId): void
    {
        $teamId = auth()->user()?->getAttribute('current_team_id');
        abort_unless(is_numeric($teamId) && (int) $teamId > 0, 403);
        $affiliate = Affiliate::query()->where('team_id', (int) $teamId)->findOrFail($affiliateId);
        app(ApproveAffiliate::class)->execute((int) $teamId, $affiliate);
        $this->resetPage();
    }

    public function render(AffiliateQuery $query): View
    {
        $teamId = auth()->user()?->getAttribute('current_team_id');
        abort_unless(is_numeric($teamId) && (int) $teamId > 0, 403);
        $search = trim($this->search);
        $affiliates = $query->affiliates((int) $teamId)->when($search !== '', fn ($builder) => $builder->where(function ($builder) use ($search): void {
            $term = '%'.addcslashes($search, '%_').'%';
            $builder->where('name', 'like', $term)->orWhere('email', 'like', $term);
        }))->when($this->status !== '', fn ($builder) => $builder->where('status', $this->status))->paginate(25);

        return view('crm-affiliate-management-livewire::affiliate-browser', compact('affiliates'));
    }
}
