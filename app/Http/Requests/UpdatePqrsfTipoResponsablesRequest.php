<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePqrsfTipoResponsablesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'pqrsf_tipo_id' => ['required', 'exists:pqrsf_tipos,id'],
            'responsables' => ['nullable', 'array'],
            'responsables.*' => ['integer', 'exists:users,id'],
        ];
    }
}
