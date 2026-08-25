<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSales\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ChannelSales\Models\ChannelOpportunity;

final class ChannelSalesQuery
{
    public function opportunities(int $teamId): Builder
    {
        return ChannelOpportunity::query()->where('team_id', $teamId)->latest();
    }

    public function forecast(int $teamId): array
    {
        return $this->opportunities($teamId)->get()->groupBy('stage')->map(fn ($rows): array => ['amount' => $rows->sum('amount'), 'weighted' => $rows->sum(fn ($row): float => (float) $row->getAttribute('amount') * (float) (($row->getAttribute('forecast')['probability'] ?? 0)))])->all();
    }
}
