<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Models;

use Illuminate\Database\Eloquent\Model;

final class IdealCustomerProfile extends Model
{
    protected $table = 'crm_ideal_customer_profiles';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['criteria' => 'array', 'active' => 'boolean'];
    }
}
