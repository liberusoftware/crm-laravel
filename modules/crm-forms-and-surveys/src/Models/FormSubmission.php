<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveys\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 */
final class FormSubmission extends Model
{
    protected $table = 'crm_forms_submissions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['consent' => 'boolean', 'attribution' => 'array', 'payload' => 'array'];
    }
}
