<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveys\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $team_id
 * @property string $kind
 * @property string $status
 */
final class SurveyForm extends Model
{
    protected $table = 'crm_forms_surveys';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['schema' => 'array', 'settings' => 'array', 'embedding' => 'array'];
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class, 'form_id');
    }
}
