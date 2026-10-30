<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_barras' => 'required|string|max:50|unique:productos,codigo_barras',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0|gt:precio_compra',
            'stock' => 'sometimes|integer|min:0',
            'stock_minimo' => 'sometimes|integer|min:0',
            'lote' => 'nullable|string|max:50',
            'fecha_vencimiento' => 'nullable|date|after:today',
            'requiere_receta' => 'sometimes|boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_barras.unique' => 'Ya existe un producto con ese código de barras.',
            'precio_venta.gt' => 'El precio de venta debe ser mayor al precio de compra.',
            'fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a hoy.',
        ];
    }
}