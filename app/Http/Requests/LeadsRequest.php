<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\LeadOrigin;

class LeadsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'email' => ['required','email'],
            'name' => ['nullable','string','min:2','max:255'],
            'phone' => ['nullable','string','max:20','regex:/^[0-9+\-\s()]{6,20}$/'],
            'origin' => ['required', new Enum(LeadOrigin::class)],
            'consent' => ['accepted'],
            'recaptcha_token' => ['required','string'],
        ];
    }
}
