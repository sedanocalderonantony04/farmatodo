<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_barras' => 'sometimes|string|max:50|unique:productos,codigo_barras,' . $this->producto->id,
            'nombre' => 'sometimes|string|max:200',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'sometimes|exists:categorias,id',
            'proveedor_id' => 'sometimes|exists:proveedores,id',
            'precio_compra' => 'sometimes|numeric|min:0',
            'precio_venta' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'stock_minimo' => 'sometimes|integer|min:0',
            'lote' => 'nullable|string|max:50',
            'fecha_vencimiento' => 'nullable|date',
            'requiere_receta' => 'sometimes|boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado' => 'sometimes|boolean',
        ];
    }
}