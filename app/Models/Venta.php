<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_venta', 'cliente_id', 'user_id', 'subtotal',
        'descuento', 'igv', 'total', 'estado', 'fecha_venta',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2', 'descuento' => 'decimal:2',
        'igv' => 'decimal:2', 'total' => 'decimal:2', 'fecha_venta' => 'datetime',
    ];

    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function detalles() { return $this->hasMany(DetalleVenta::class); }
    public function pagos() { return $this->hasMany(Pago::class); }

    public function scopePagadas($query) { return $query->where('estado', 'pagada'); }
}