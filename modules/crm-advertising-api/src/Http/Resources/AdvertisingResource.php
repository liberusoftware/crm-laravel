<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvertisingApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\CRM\Advertising\Models\AdvertisingRecord;

/** @mixin AdvertisingRecord */
final class AdvertisingResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->resource->getKey(),
            'type' => 'crm-advertising',
            'attributes' => [
                'kind' => $this->kind,
                'name' => $this->name,
                'status' => $this->status,
                'external_id' => $this->external_id,
                'platform' => $this->platform,
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
