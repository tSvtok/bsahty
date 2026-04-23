<?php

namespace App\Http\Requests;

use App\Enums\SportCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'string'],
            'sport_category' => ['required', new Enum(SportCategory::class)],
            'spot_id' => ['nullable', 'exists:spots,id'],
            'event_id' => ['nullable', 'exists:events,id'],
        ];
    }
}
