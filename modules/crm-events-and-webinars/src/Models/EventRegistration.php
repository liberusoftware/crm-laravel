<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinars\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $event_id
 * @property string $email
 * @property string $status
 */
final class EventRegistration extends Model
{
    use IsTenantModel;

    protected $table = 'crm_event_registrations';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['checked_in_at' => 'datetime', 'metadata' => 'array'];
    }
}
