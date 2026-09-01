<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistration\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property string $status
 * @property string $contact_email
 * @property string|null $territory
 * @property array<int, mixed>|null $collaborators
 */
final class DealRegistration extends Model
{
    use IsTenantModel;

    protected $table = 'crm_deal_registrations';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['protection_until' => 'datetime', 'attribution' => 'array', 'collaborators' => 'array'];
    }
}
