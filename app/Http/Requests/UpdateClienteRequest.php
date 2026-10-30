<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:150',
            'tipo_documento' => 'sometimes|in:DNI,RUC,CE,PASAPORTE',
            'numero_documento' => 'sometimes|string|max:20|unique:clientes,numero_documento,' . $this->cliente->id,
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date',
            'estado' => 'sometimes|boolean',
        ];
    }
}