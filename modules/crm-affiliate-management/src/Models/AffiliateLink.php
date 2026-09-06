<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagement\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $affiliate_id
 * @property string $status
 */
final class AffiliateLink extends Model
{
    use IsTenantModel;

    protected $table = 'crm_affiliate_links';

    protected $guarded = [];
}
