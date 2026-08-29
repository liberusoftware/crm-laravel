<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Models;

use Illuminate\Database\Eloquent\Model;

final class ProspectExport extends Model
{
    protected $table = 'crm_prospect_exports';

    protected $guarded = [];
}
