<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveys\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 */
final class FormSubmission extends Model
{
    use IsTenantModel;

    protected $table = 'crm_forms_submissions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['consent' => 'boolean', 'attribution' => 'array', 'payload' => 'array'];
    }
}
