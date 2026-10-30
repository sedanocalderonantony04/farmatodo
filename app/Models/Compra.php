<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_compra', 'proveedor_id', 'user_id', 'subtotal',
        'igv', 'total', 'estado', 'fecha_compra',
    ];

    protected $casts = ['subtotal' => 'decimal:2', 'igv' => 'decimal:2', 'total' => 'decimal:2', 'fecha_compra' => 'datetime'];

    public function proveedor() { return $this->belongsTo(Proveedor::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function detalles() { return $this->hasMany(DetalleCompra::class); }
}