<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario'; // ← LA CLAVE DEL ARREGLO

    protected $fillable = [
        'producto_id', 'tipo', 'cantidad', 'stock_anterior', 'stock_nuevo',
        'motivo', 'user_id', 'referencia', 'fecha_movimiento',
    ];

    protected $casts = [
        'fecha_movimiento' => 'datetime',
    ];

    public function producto() { return $this->belongsTo(Producto::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function scopeEntradas($query) { return $query->where('tipo', 'entrada'); }
    public function scopeSalidas($query) { return $query->where('tipo', 'salida'); }
}