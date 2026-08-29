<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Models;

use Illuminate\Database\Eloquent\Model;

/** @property string $kind @property string $status @property array<string,mixed>|null $conditions @property array<string,mixed>|null $exclusions @property int $estimated_count */
final class Audience extends Model
{
    protected $table = 'crm_segmentation_audiences';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['conditions' => 'array', 'exclusions' => 'array', 'calculated_attributes' => 'array', 'refreshed_at' => 'datetime'];
    }
}
