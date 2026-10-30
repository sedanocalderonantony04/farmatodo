<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\MovimientoInventario;
use App\Models\Venta;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function ventas(Request $request)
    {
        $query = Venta::with(['cliente', 'user', 'detalles.producto']);

        if ($request->has('fecha_desde') && $request->has('fecha_hasta')) {
            $query->whereBetween('fecha_venta', [
                $request->fecha_desde,
                $request->fecha_hasta . ' 23:59:59'
            ]);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        $ventas = $query->orderBy('fecha_venta', 'desc')->get();

        $total = $ventas->sum('total');

        return response()->json([
            'success' => true,
            'data' => $ventas,
            'total' => round($total, 2),
        ]);
    }

    public function compras(Request $request)
    {
        $query = Compra::with(['proveedor', 'user', 'detalles.producto']);

        if ($request->has('fecha_desde') && $request->has('fecha_hasta')) {
            $query->whereBetween('fecha_compra', [
                $request->fecha_desde,
                $request->fecha_hasta . ' 23:59:59'
            ]);
        }

        if ($request->has('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        $compras = $query->orderBy('fecha_compra', 'desc')->get();

        $total = $compras->sum('total');

        return response()->json([
            'success' => true,
            'data' => $compras,
            'total' => round($total, 2),
        ]);
    }

    public function inventario(Request $request)
    {
        $query = MovimientoInventario::with(['producto', 'user']);

        if ($request->has('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('fecha_desde') && $request->has('fecha_hasta')) {
            $query->whereBetween('fecha_movimiento', [
                $request->fecha_desde,
                $request->fecha_hasta . ' 23:59:59'
            ]);
        }

        $movimientos = $query->orderBy('fecha_movimiento', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $movimientos,
        ]);
    }

    public function stockBajo()
    {
        $productos = \App\Models\Producto::with(['categoria', 'proveedor'])
            ->stockBajo()
            ->orderBy('stock', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $productos,
        ]);
    }

    public function productosVencidos()
    {
        $productos = \App\Models\Producto::with(['categoria', 'proveedor'])
            ->vencidos()
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $productos,
        ]);
    }
}