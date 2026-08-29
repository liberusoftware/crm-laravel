<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccess\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $customer_id @property string $status */
final class SuccessRisk extends Model
{
    protected $table = 'crm_success_risks';

    protected $guarded = [];
}
