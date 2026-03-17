<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignPqrsfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'asignado_a' => ['required', 'exists:users,id'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
