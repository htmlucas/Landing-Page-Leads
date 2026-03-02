<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Enums\LeadOrigin;

class LeadsUpdateRequest extends FormRequest
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
            'email' => ['required','email', Rule::unique('leads', 'email')->ignore($this->lead)],
            'name' => ['nullable','string','min:2','max:255'],
            'phone' => ['nullable','string','max:20','regex:/^[0-9+\-\s()]{6,20}$/'],
            'origins' => ['required', 'array', 'min:1'],
            'origins.*' => [new Enum(LeadOrigin::class)],
        ];
    }
}
