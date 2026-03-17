<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIntegrationConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'fuente_activa' => ['required', Rule::in(['excel', 'api'])],
            'api_base_url' => ['nullable', 'url'],
            'api_token' => ['nullable', 'string', 'max:255'],
            'api_timeout' => ['nullable', 'integer', 'between:5,120'],
            'api_username' => ['nullable', 'string', 'max:120'],
            'api_password' => ['nullable', 'string', 'max:120'],
        ];
    }
}
