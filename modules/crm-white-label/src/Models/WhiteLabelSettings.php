<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property string|null $brand_name
 * @property string|null $custom_domain
 * @property string $theme
 * @property string $provider
 * @property array<string, mixed>|null $email_settings
 * @property array<string, mixed>|null $application_settings
 * @property array<string, mixed>|null $client_experience
 * @property bool $show_platform_attribution
 * @property int $version
 */
final class WhiteLabelSettings extends Model
{
    use IsTenantModel;

    protected $table = 'crm_white_label_settings';

    protected $fillable = ['team_id', 'brand_name', 'custom_domain', 'theme', 'email_settings', 'application_settings', 'client_experience', 'provider', 'show_platform_attribution', 'version'];

    protected function casts(): array
    {
        return ['email_settings' => 'array', 'application_settings' => 'array', 'client_experience' => 'array', 'show_platform_attribution' => 'boolean', 'version' => 'integer'];
    }
}
