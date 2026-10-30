<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:150',
            'ruc' => 'sometimes|string|max:20|unique:proveedores,ruc,' . $this->proveedor->id,
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string',
            'contacto' => 'nullable|string|max:100',
            'estado' => 'sometimes|boolean',
        ];
    }
}