<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanningApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Liberu\CRM\AccountPlanning\Models\AccountPlanningRecord;

/**
 * @mixin AccountPlanningRecord
 *
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 */
final class AccountPlanningResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->resource->getKey(),
            'type' => 'crm-account-planning',
            'attributes' => [
                'kind' => $this->kind,
                'name' => $this->name,
                'status' => $this->status,
                'account_id' => $this->account_id,
                'owner_id' => $this->owner_id,
                'payload' => $this->payload ?? [],
                'starts_at' => $this->starts_at?->toISOString(),
                'ends_at' => $this->ends_at?->toISOString(),
                'completed_at' => $this->completed_at?->toISOString(),
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
            ],
        ];
    }
}
