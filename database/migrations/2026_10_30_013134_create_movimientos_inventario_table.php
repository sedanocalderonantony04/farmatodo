<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->enum('tipo', ['entrada', 'salida', 'ajuste', 'devolucion', 'vencimiento']);
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->string('motivo', 200)->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('referencia', 100)->nullable();
            $table->timestamp('fecha_movimiento')->useCurrent();
            $table->timestamps();

            $table->index(['producto_id', 'fecha_movimiento', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};