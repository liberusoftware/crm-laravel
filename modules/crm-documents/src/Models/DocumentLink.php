<?php

declare(strict_types=1);

namespace Liberu\CRM\Documents\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $document_id @property string $status */
final class DocumentLink extends Model
{
    use IsTenantModel;

    protected $table = 'crm_document_links';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime'];
    }
}
