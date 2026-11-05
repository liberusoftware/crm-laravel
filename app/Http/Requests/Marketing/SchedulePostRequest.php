<?php

declare(strict_types=1);

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

final class SchedulePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['scheduled_at' => ['required', 'date', 'after:now']];
    }
}
