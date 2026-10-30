<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompraRequest;
use App\Http\Requests\UpdateCompraRequest;
use App\Http\Resources\CompraResource;
use App\Http\Resources\DetalleCompraResource;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'user']);

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('search')) {
            $query->where('numero_compra', 'like', '%' . $request->search . '%');
        }

        $compras = $query->orderBy('fecha_compra', 'desc')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => CompraResource::collection($compras),
            'meta' => [
                'total' => $compras->total(),
                'per_page' => $compras->perPage(),
                'current_page' => $compras->currentPage(),
                'last_page' => $compras->lastPage(),
            ],
        ]);
    }

    public function store(StoreCompraRequest $request)
    {
        DB::beginTransaction();
        try {
            $lastCompra = Compra::orderBy('id', 'desc')->first();
            $numeroCompra = $lastCompra ? 'C-' . str_pad($lastCompra->id + 1, 6, '0', STR_PAD_LEFT) : 'C-000001';

            $compra = Compra::create([
                'numero_compra' => $numeroCompra,
                'proveedor_id' => $request->proveedor_id,
                'user_id' => Auth::id(),
                'subtotal' => $request->subtotal,
                'igv' => $request->igv ?? 0,
                'total' => $request->total,
                'estado' => 'completada',
                'fecha_compra' => $request->fecha_compra ?? now(),
            ]);

            foreach ($request->productos as $productoData) {
                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $productoData['producto_id'],
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $productoData['precio_unitario'],
                    'subtotal' => $productoData['cantidad'] * $productoData['precio_unitario'],
                ]);

                $producto = Producto::find($productoData['producto_id']);
                $stockAnterior = $producto->stock;
                $producto->stock += $productoData['cantidad'];
                
                if (isset($productoData['precio_compra'])) {
                    $producto->precio_compra = $productoData['precio_compra'];
                }
                
                if (isset($productoData['lote'])) {
                    $producto->lote = $productoData['lote'];
                }
                if (isset($productoData['fecha_vencimiento'])) {
                    $producto->fecha_vencimiento = $productoData['fecha_vencimiento'];
                }
                
                $producto->save();

                MovimientoInventario::create([
                    'producto_id' => $productoData['producto_id'],
                    'tipo' => 'entrada',
                    'cantidad' => $productoData['cantidad'],
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $producto->stock,
                    'motivo' => 'Compra a proveedor',
                    'user_id' => Auth::id(),
                    'referencia' => $numeroCompra,
                    'fecha_movimiento' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compra registrada exitosamente.',
                'data' => new CompraResource($compra->load(['proveedor', 'user', 'detalles.producto'])),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la compra: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Compra $compra)
    {
        $compra->load(['proveedor', 'user', 'detalles.producto']);

        return response()->json([
            'success' => true,
            'data' => new CompraResource($compra),
        ]);
    }

    public function update(UpdateCompraRequest $request, Compra $compra)
    {
        if ($compra->estado === 'completada') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede modificar una compra completada.',
            ], 400);
        }

        $compra->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Compra actualizada exitosamente.',
            'data' => new CompraResource($compra),
        ]);
    }

    public function destroy(Compra $compra)
    {
        if ($compra->estado === 'completada') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar una compra completada.',
            ], 400);
        }

        $compra->delete();

        return response()->json([
            'success' => true,
            'message' => 'Compra eliminada exitosamente.',
        ]);
    }
}