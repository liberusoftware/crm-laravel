<?php

declare(strict_types=1);

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

final class CampaignOperationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['operation' => ['required', 'in:create,update,activate,pause,archive,analytics']];
    }
}
