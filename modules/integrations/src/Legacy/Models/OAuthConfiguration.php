<?php

namespace App\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $service_name
 * @property string|null $client_id
 * @property string|null $client_secret
 */
class OAuthConfiguration extends Model
{
    use HasFactory;
    use IsTenantModel;

    protected $table = 'oauth_configurations';

    protected $fillable = [
        'user_id',
        'service_name',
        'client_id',
        'client_secret',
        'additional_settings',
        'is_active',
        'account_name',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    protected $casts = [
        'client_secret' => 'encrypted',
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'token_expires_at' => 'datetime',
        'additional_settings' => 'array',
        'is_active' => 'boolean',
    ];

    public static function getConfig($serviceName)
    {
        return self::where('service_name', $serviceName)->first();
    }
}
