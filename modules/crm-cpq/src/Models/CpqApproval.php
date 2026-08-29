<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQ\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $quote_id @property string $status */
final class CpqApproval extends Model
{
    protected $table = 'crm_cpq_approvals';

    protected $guarded = [];
}
