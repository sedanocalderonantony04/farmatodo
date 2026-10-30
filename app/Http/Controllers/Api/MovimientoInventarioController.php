<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMovimientoInventarioRequest;
use App\Http\Resources\MovimientoInventarioResource;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovimientoInventarioController extends Controller
{
    public function index(Request $request)
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
                $request->fecha_hasta,
            ]);
        }

        $movimientos = $query->orderBy('fecha_movimiento', 'desc')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => MovimientoInventarioResource::collection($movimientos),
            'meta' => [
                'total' => $movimientos->total(),
                'per_page' => $movimientos->perPage(),
                'current_page' => $movimientos->currentPage(),
                'last_page' => $movimientos->lastPage(),
            ],
        ]);
    }

    public function store(StoreMovimientoInventarioRequest $request)
    {
        $producto = Producto::find($request->producto_id);
        $stockAnterior = $producto->stock;

        $nuevoStock = match($request->tipo) {
            'entrada' => $stockAnterior + $request->cantidad,
            'salida' => $stockAnterior - $request->cantidad,
            'ajuste' => $request->cantidad,
            'devolucion' => $stockAnterior + $request->cantidad,
            'vencimiento' => $stockAnterior - $request->cantidad,
        };

        if ($request->tipo === 'salida' && $nuevoStock < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente para realizar esta salida.',
            ], 400);
        }

        $movimiento = MovimientoInventario::create([
            'producto_id' => $request->producto_id,
            'tipo' => $request->tipo,
            'cantidad' => $request->cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $nuevoStock,
            'motivo' => $request->motivo,
            'user_id' => Auth::id(),
            'referencia' => $request->referencia,
            'fecha_movimiento' => $request->fecha_movimiento ?? now(),
        ]);

        $producto->stock = $nuevoStock;
        $producto->save();

        return response()->json([
            'success' => true,
            'message' => 'Movimiento de inventario registrado exitosamente.',
            'data' => new MovimientoInventarioResource($movimiento->load(['producto', 'user'])),
        ], 201);
    }

    public function show(MovimientoInventario $movimientoInventario)
    {
        return response()->json([
            'success' => true,
            'data' => new MovimientoInventarioResource($movimientoInventario->load(['producto', 'user'])),
        ]);
    }
}