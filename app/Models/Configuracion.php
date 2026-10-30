<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuraciones'; // ← AGREGA ESTA LÍNEA

    protected $fillable = ['clave', 'valor', 'descripcion'];

    public static function get(string $clave, $default = null)
    {
        return self::where('clave', $clave)->value('valor') ?? $default;
    }

    public static function set(string $clave, $valor, string $descripcion = null)
    {
        return self::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor, 'descripcion' => $descripcion]
        );
    }
}