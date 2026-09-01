<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Attachment extends Model
{
    use IsTenantModel;

    protected $table = 'crm_core_attachments';

    protected $fillable = ['team_id', 'uploaded_by', 'disk', 'path', 'name', 'mime_type', 'size', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'size' => 'integer'];
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
