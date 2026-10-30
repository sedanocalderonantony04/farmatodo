<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = ['venta_id', 'metodo_pago', 'monto', 'referencia', 'fecha_pago'];

    protected $casts = ['monto' => 'decimal:2', 'fecha_pago' => 'datetime'];

    public function venta() { return $this->belongsTo(Venta::class); }
}