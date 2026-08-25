<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Models;

use Illuminate\Database\Eloquent\Model;

final class SegmentationLineage extends Model
{
    protected $table = 'crm_segmentation_lineage';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['details' => 'array'];
    }
}
