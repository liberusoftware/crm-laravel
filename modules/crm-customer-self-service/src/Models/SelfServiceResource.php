<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfService\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $kind @property bool $published */
final class SelfServiceResource extends Model
{
    protected $table = 'crm_self_service_resources';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }
}
