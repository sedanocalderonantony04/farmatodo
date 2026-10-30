<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovimientoInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'producto_id' => 'required|exists:productos,id',
            'tipo' => 'required|in:entrada,salida,ajuste,devolucion,vencimiento',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:200',
            'referencia' => 'nullable|string|max:100',
            'fecha_movimiento' => 'sometimes|date',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.in' => 'El tipo de movimiento debe ser: entrada, salida, ajuste, devolucion o vencimiento.',
        ];
    }
}