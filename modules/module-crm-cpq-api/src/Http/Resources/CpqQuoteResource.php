<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Liberu\CRM\CPQ\Models\CpqQuote;

/**
 * @mixin CpqQuote
 *
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 */
final class CpqQuoteResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'crm-cpq-quote', 'attributes' => ['name' => $this->name, 'status' => $this->status, 'currency' => $this->currency, 'configuration' => $this->configuration ?? [], 'lines' => $this->lines ?? [], 'subtotal' => $this->subtotal, 'discount' => $this->discount, 'total' => $this->total, 'margin' => $this->margin, 'created_at' => $this->created_at?->toISOString(), 'updated_at' => $this->updated_at?->toISOString()]];
    }
}
