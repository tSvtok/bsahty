<?php

namespace App\Http\Requests;

use App\Enums\EventStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['sometimes', 'date', 'after:now'],
            'max_participants' => ['sometimes', 'integer', 'min:2'],
            'status' => ['sometimes', new Enum(EventStatus::class)],
            'join' => ['sometimes', 'boolean'],
        ];
    }
}
