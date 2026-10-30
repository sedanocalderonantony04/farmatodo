<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:100|unique:categorias,nombre,' . $this->categoria->id,
            'descripcion' => 'nullable|string',
            'estado' => 'sometimes|boolean',
        ];
    }
}