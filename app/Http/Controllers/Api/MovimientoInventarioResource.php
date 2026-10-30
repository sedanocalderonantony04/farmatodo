<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovimientoInventarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'producto' => [
                'id' => $this->producto->id,
                'nombre' => $this->producto->nombre,
                'codigo_barras' => $this->producto->codigo_barras,
            ],
            'tipo' => $this->tipo,
            'cantidad' => $this->cantidad,
            'stock_anterior' => $this->stock_anterior,
            'stock_nuevo' => $this->stock_nuevo,
            'motivo' => $this->motivo,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'referencia' => $this->referencia,
            'fecha_movimiento' => $this->fecha_movimiento->format('Y-m-d H:i:s'),
        ];
    }
}