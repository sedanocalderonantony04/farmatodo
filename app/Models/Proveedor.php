<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proveedores'; // ← AGREGA ESTA LÍNEA

    protected $fillable = ['nombre', 'ruc', 'telefono', 'email', 'direccion', 'contacto', 'estado'];
    protected $casts = ['estado' => 'boolean'];

    public function productos() { return $this->hasMany(Producto::class); }
    public function compras() { return $this->hasMany(Compra::class); }
}