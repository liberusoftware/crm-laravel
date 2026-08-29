<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagement\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $affiliate_id
 * @property string $status
 */
final class AffiliateLink extends Model
{
    protected $table = 'crm_affiliate_links';

    protected $guarded = [];
}
