<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePqrsfResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in(['nota_interna', 'respuesta_ciudadano', 'cierre'])],
            'mensaje' => ['required', 'string', 'min:5', 'max:8000'],
            'notificado' => ['nullable', 'boolean'],
        ];
    }
}
