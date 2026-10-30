<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PagoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'metodo_pago' => $this->metodo_pago,
            'monto' => $this->monto,
            'referencia' => $this->referencia,
            'fecha_pago' => $this->fecha_pago?->format('Y-m-d H:i:s'),
        ];
    }
}