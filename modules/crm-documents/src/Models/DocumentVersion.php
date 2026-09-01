<?php

declare(strict_types=1);

namespace Liberu\CRM\Documents\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $document_id @property int $version */
final class DocumentVersion extends Model
{
    use IsTenantModel;

    protected $table = 'crm_document_versions';

    protected $guarded = [];
}
