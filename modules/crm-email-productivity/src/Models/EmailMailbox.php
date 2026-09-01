<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivity\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $provider @property string $status */
final class EmailMailbox extends Model
{
    use IsTenantModel;

    protected $table = 'crm_email_mailboxes';

    protected $guarded = [];

    protected $hidden = ['credential_reference'];

    protected function casts(): array
    {
        return ['last_synced_at' => 'datetime'];
    }
}
