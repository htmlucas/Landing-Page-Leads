<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestDeletionRequest extends FormRequest
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
            'email' => ['required','email', 'exists:leads,email'],
            'consent' => ['accepted'],
            'recaptcha_token' => ['required','string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'If the email exists, you will receive instructions.',
        ];
    }
}
