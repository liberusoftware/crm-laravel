<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnels\Models;

use Illuminate\Database\Eloquent\Model;

/** @property string $status @property string $kind */
final class FunnelPage extends Model
{
    protected $table = 'crm_funnel_pages';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['seo' => 'array', 'personalization' => 'array', 'form' => 'array'];
    }
}
