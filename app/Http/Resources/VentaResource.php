<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VentaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero_venta' => $this->numero_venta,
            'cliente' => $this->whenLoaded('cliente', fn () => [
                'id' => $this->cliente->id,
                'nombre' => $this->cliente->nombre,
            ]),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'subtotal' => $this->subtotal,
            'descuento' => $this->descuento,
            'igv' => $this->igv,
            'total' => $this->total,
            'estado' => $this->estado,
            'fecha_venta' => $this->fecha_venta->format('Y-m-d H:i:s'),
            'detalles' => DetalleVentaResource::collection($this->whenLoaded('detalles')),
            'pagos' => PagoResource::collection($this->whenLoaded('pagos')),
        ];
    }
}