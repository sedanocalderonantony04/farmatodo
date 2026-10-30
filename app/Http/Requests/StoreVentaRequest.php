<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'nullable|exists:clientes,id',
            'descuento' => 'sometimes|numeric|min:0',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.descuento' => 'sometimes|numeric|min:0',
            'pagos' => 'required|array|min:1',
            'pagos.*.metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,yape,plin',
            'pagos.*.monto' => 'required|numeric|min:0',
            'pagos.*.referencia' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'productos.required' => 'La venta debe tener al menos un producto.',
            'pagos.required' => 'La venta debe registrar al menos un pago.',
            'pagos.*.metodo_pago.in' => 'Método de pago no válido.',
        ];
    }
}