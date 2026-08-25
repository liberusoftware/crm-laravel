<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Models;

use Illuminate\Database\Eloquent\Model;

final class AgentReview extends Model
{
    protected $table = 'crm_service_agent_reviews';

    protected $guarded = [];
}
