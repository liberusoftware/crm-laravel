<?php

declare(strict_types=1);

namespace Liberu\CRM\Documents\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $status @property string $storage_key */
final class CrmDocument extends Model
{
    use IsTenantModel;

    protected $table = 'crm_documents';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['retention_until' => 'datetime', 'access' => 'array'];
    }
}
