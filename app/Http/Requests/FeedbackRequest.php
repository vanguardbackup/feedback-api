<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'experiment' => ['required', 'string', 'max:255'],
            'feedback' => ['required', 'string', 'max:10000'],
            'php_version' => ['required', 'string', 'max:20'],
            'vanguard_version' => ['required', 'string', 'max:20'],
            'email_address' => ['nullable', 'email', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'experiment.required' => 'The experiment name is required.',
            'experiment.max' => 'The experiment name must not exceed 255 characters.',
            'feedback.required' => 'The feedback content is required.',
            'feedback.max' => 'The feedback must not exceed 10000 characters.',
            'php_version.required' => 'The PHP version is required.',
            'php_version.max' => 'The PHP version must not exceed 20 characters.',
            'vanguard_version.required' => 'The Vanguard version is required.',
            'vanguard_version.max' => 'The Vanguard version must not exceed 20 characters.',
            'email_address.email' => 'Please provide a valid email address.',
            'email_address.max' => 'The email address must not exceed 255 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'experiment' => trim($this->experiment),
            'feedback' => trim($this->feedback),
            'php_version' => trim($this->php_version),
            'vanguard_version' => trim($this->vanguard_version),
            'email_address' => $this->email_address ? trim($this->email_address) : null,
        ]);
    }
}
