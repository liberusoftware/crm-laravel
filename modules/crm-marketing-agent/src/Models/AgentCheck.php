<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgent\Models;

use Illuminate\Database\Eloquent\Model;

/** @property string $kind @property string $status */
final class AgentCheck extends Model
{
    protected $table = 'crm_marketing_agent_checks';

    protected $guarded = [];
}
