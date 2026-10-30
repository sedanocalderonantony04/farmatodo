<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVentaRequest;
use App\Http\Resources\VentaResource;
use App\Models\Configuracion;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'user']);

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('fecha_desde') && $request->has('fecha_hasta')) {
            $query->whereBetween('fecha_venta', [$request->fecha_desde, $request->fecha_hasta . ' 23:59:59']);
        }

        if ($request->has('search')) {
            $query->where('numero_venta', 'like', '%' . $request->search . '%');
        }

        $ventas = $query->orderBy('fecha_venta', 'desc')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => VentaResource::collection($ventas),
            'meta' => [
                'total' => $ventas->total(),
                'per_page' => $ventas->perPage(),
                'current_page' => $ventas->currentPage(),
                'last_page' => $ventas->lastPage(),
            ],
        ]);
    }

    public function store(StoreVentaRequest $request)
    {
        DB::beginTransaction();
        try {
            $iva = (float) Configuracion::get('iva', 16);

            $lastVenta = Venta::withTrashed()->orderBy('id', 'desc')->first();
            $numeroVenta = $lastVenta ? 'V-' . str_pad($lastVenta->id + 1, 6, '0', STR_PAD_LEFT) : 'V-000001';

            // Validar stock y calcular líneas
            $lineas = [];
            $sumaSubtotales = 0;

            foreach ($request->productos as $item) {
                $producto = Producto::find($item['producto_id']);
                $cantidad = $item['cantidad'];

                if ($producto->stock < $cantidad) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock}, solicitado: {$cantidad}.",
                    ], 400);
                }

                $descLinea = $item['descuento'] ?? 0;
                $subtotalLinea = ($producto->precio_venta * $cantidad) - $descLinea;

                $lineas[] = [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $producto->precio_venta,
                    'descuento' => $descLinea,
                    'subtotal' => $subtotalLinea,
                ];

                $sumaSubtotales += $subtotalLinea;
            }

            // Totales (IVA incluido en el precio de venta)
            $descuentoGlobal = $request->descuento ?? 0;
            $total = round(max($sumaSubtotales - $descuentoGlobal, 0), 2);
            $subtotal = round($total / (1 + $iva / 100), 2);
            $igv = round($total - $subtotal, 2);

            $venta = Venta::create([
                'numero_venta' => $numeroVenta,
                'cliente_id' => $request->cliente_id,
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'descuento' => $descuentoGlobal,
                'igv' => $igv,
                'total' => $total,
                'estado' => 'pendiente',
                'fecha_venta' => now(),
            ]);

            // Detalles + descuento de stock + kardex
            foreach ($lineas as $linea) {
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $linea['producto']->id,
                    'cantidad' => $linea['cantidad'],
                    'precio_unitario' => $linea['precio_unitario'],
                    'descuento' => $linea['descuento'],
                    'subtotal' => $linea['subtotal'],
                ]);

                $stockAnterior = $linea['producto']->stock;
                $linea['producto']->stock = $stockAnterior - $linea['cantidad'];
                $linea['producto']->save();

                MovimientoInventario::create([
                    'producto_id' => $linea['producto']->id,
                    'tipo' => 'salida',
                    'cantidad' => $linea['cantidad'],
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $linea['producto']->stock,
                    'motivo' => 'Venta',
                    'user_id' => Auth::id(),
                    'referencia' => $numeroVenta,
                    'fecha_movimiento' => now(),
                ]);
            }

            // Pagos y estado
            $sumaPagos = 0;
            foreach ($request->pagos as $pagoData) {
                Pago::create([
                    'venta_id' => $venta->id,
                    'metodo_pago' => $pagoData['metodo_pago'],
                    'monto' => $pagoData['monto'],
                    'referencia' => $pagoData['referencia'] ?? null,
                    'fecha_pago' => now(),
                ]);
                $sumaPagos += $pagoData['monto'];
            }

            $venta->estado = $sumaPagos >= $total ? 'pagada' : 'pendiente';
            $venta->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada exitosamente.',
                'data' => new VentaResource($venta->load(['cliente', 'user', 'detalles.producto', 'pagos'])),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la venta: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'user', 'detalles.producto', 'pagos']);

        return response()->json([
            'success' => true,
            'data' => new VentaResource($venta),
        ]);
    }

    public function anular(Venta $venta)
    {
        if ($venta->estado === 'anulada') {
            return response()->json([
                'success' => false,
                'message' => 'Esta venta ya está anulada.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            foreach ($venta->detalles as $detalle) {
                $producto = $detalle->producto;
                $stockAnterior = $producto->stock;
                $producto->stock = $stockAnterior + $detalle->cantidad;
                $producto->save();

                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'devolucion',
                    'cantidad' => $detalle->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $producto->stock,
                    'motivo' => 'Anulación de venta ' . $venta->numero_venta,
                    'user_id' => Auth::id(),
                    'referencia' => $venta->numero_venta,
                    'fecha_movimiento' => now(),
                ]);
            }

            $venta->estado = 'anulada';
            $venta->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta anulada y stock revertido exitosamente.',
                'data' => new VentaResource($venta->load(['detalles.producto', 'pagos'])),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al anular la venta: ' . $e->getMessage(),
            ], 500);
        }
    }
}