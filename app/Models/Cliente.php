<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre', 'tipo_documento', 'numero_documento', 'telefono',
        'email', 'direccion', 'fecha_nacimiento', 'estado',
    ];

    protected $casts = ['estado' => 'boolean', 'fecha_nacimiento' => 'date'];

    public function ventas() { return $this->hasMany(Venta::class); }
}