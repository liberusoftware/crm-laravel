<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Models;

use Illuminate\Database\Eloquent\Model;

final class AudienceMember extends Model
{
    protected $table = 'crm_segmentation_members';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['attributes' => 'array', 'included_at' => 'datetime'];
    }
}
