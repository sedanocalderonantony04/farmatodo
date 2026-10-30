<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo_barras', 'nombre', 'descripcion', 'categoria_id', 'proveedor_id',
        'precio_compra', 'precio_venta', 'stock', 'stock_minimo',
        'lote', 'fecha_vencimiento', 'requiere_receta', 'imagen', 'estado',
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'requiere_receta' => 'boolean',
        'estado' => 'boolean',
        'fecha_vencimiento' => 'date',
    ];

    public function categoria() { return $this->belongsTo(Categoria::class); }
    public function proveedor() { return $this->belongsTo(Proveedor::class); }
    public function detalleVentas() { return $this->hasMany(DetalleVenta::class); }
    public function movimientos() { return $this->hasMany(MovimientoInventario::class); }
    public function detalleCompras() { return $this->hasMany(DetalleCompra::class); }

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock', '<=', 'stock_minimo')->where('estado', true);
    }

    public function scopeVencidos($query)
    {
        return $query->where('fecha_vencimiento', '<', now())->where('estado', true);
    }
}