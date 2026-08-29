<?php

declare(strict_types=1);

namespace Liberu\CRM\Documents\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $document_id @property int $version */
final class DocumentVersion extends Model
{
    protected $table = 'crm_document_versions';

    protected $guarded = [];
}
