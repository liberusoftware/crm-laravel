<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property string $kind
 * @property string $status
 * @property array<int, array<string, mixed>> $schema
 */
final class CaptureForm extends Model
{
    protected $table = 'crm_lead_capture_forms';

    protected $fillable = ['team_id', 'actor_id', 'kind', 'name', 'slug', 'status', 'schema', 'settings', 'submissions_count'];

    protected function casts(): array
    {
        return ['schema' => 'array', 'settings' => 'array'];
    }
}
