<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePqrsfSlaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'pqrsf_tipo_id' => ['required', 'exists:pqrsf_tipos,id'],
            'prioridad' => ['required', 'in:baja,media,alta,critica'],
            'dias_respuesta' => ['required', 'integer', 'between:1,120'],
        ];
    }
}
