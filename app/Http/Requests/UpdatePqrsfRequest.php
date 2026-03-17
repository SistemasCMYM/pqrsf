<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePqrsfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'pqrsf_estado_id' => ['nullable', 'exists:pqrsf_estados,id'],
            'destinatario_id' => ['nullable', 'exists:pqrsf_destinatarios,id'],
            'prioridad' => ['nullable', Rule::in(['baja', 'media', 'alta', 'critica'])],
            'asignado_a' => ['nullable', 'exists:users,id'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
