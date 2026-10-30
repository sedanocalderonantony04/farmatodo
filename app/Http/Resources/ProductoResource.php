<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo_barras' => $this->codigo_barras,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria' => new CategoriaResource($this->whenLoaded('categoria')),
            'proveedor' => new ProveedorResource($this->whenLoaded('proveedor')),
            'precio_compra' => $this->precio_compra,
            'precio_venta' => $this->precio_venta,
            'stock' => $this->stock,
            'stock_minimo' => $this->stock_minimo,
            'lote' => $this->lote,
            'fecha_vencimiento' => $this->fecha_vencimiento?->format('Y-m-d'),
            'requiere_receta' => $this->requiere_receta,
            'imagen' => $this->imagen ? asset('storage/' . $this->imagen) : null,
            'estado' => $this->estado,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}