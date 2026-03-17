<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicPqrsfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pqrsf_tipo_id' => ['required', 'exists:pqrsf_tipos,id'],
            'destinatario_id' => ['required', 'exists:pqrsf_destinatarios,id'],
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'tipo_documento' => ['required', 'string', 'max:30'],
            'numero_documento' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'ciudad' => ['nullable', 'string', 'max:120'],
            'asunto' => ['required', 'string', 'max:180'],
            'descripcion' => ['required', 'string', 'min:20', 'max:5000'],
            'acepta_tratamiento_datos' => ['accepted'],
            'files' => ['nullable', 'array', 'max:5'],
            'files.*' => ['file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx,xlsx'],
        ];
    }
}
