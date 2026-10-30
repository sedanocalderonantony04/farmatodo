<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function resumen()
    {
        $hoy = now()->startOfDay();
        $inicioMes = now()->startOfMonth();

        // Ventas del día
        $ventasHoy = Venta::whereDate('fecha_venta', $hoy)
            ->where('estado', 'pagada')
            ->sum('total');

        // Ventas del mes
        $ventasMes = Venta::whereBetween('fecha_venta', [$inicioMes, now()])
            ->where('estado', 'pagada')
            ->sum('total');

        // Total ventas del mes (cantidad)
        $totalVentasMes = Venta::whereBetween('fecha_venta', [$inicioMes, now()])
            ->where('estado', 'pagada')
            ->count();

        // Productos con stock bajo
        $stockBajo = Producto::stockBajo()->count();

        // Productos próximos a vencer (próximos 30 días)
        $porVencer = Producto::where('fecha_vencimiento', '<=', now()->addDays(30))
            ->where('fecha_vencimiento', '>=', now())
            ->where('estado', true)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'ventas_hoy' => round($ventasHoy, 2),
                'ventas_mes' => round($ventasMes, 2),
                'total_ventas_mes' => $totalVentasMes,
                'stock_bajo' => $stockBajo,
                'productos_por_vencer' => $porVencer,
            ],
        ]);
    }

    public function topProductos($cantidad = 10)
    {
        $inicioMes = now()->startOfMonth();

        $top = DetalleVenta::select('producto_id', DB::raw('SUM(cantidad) as total_vendido'), DB::raw('SUM(subtotal) as total_ingresos'))
            ->whereHas('venta', function ($query) use ($inicioMes) {
                $query->whereBetween('fecha_venta', [$inicioMes, now()])
                      ->where('estado', 'pagada');
            })
            ->groupBy('producto_id')
            ->orderBy('total_vendido', 'desc')
            ->limit($cantidad)
            ->with('producto')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $top,
        ]);
    }

    public function ventasPorDia()
    {
        $inicioMes = now()->startOfMonth();

        $ventas = Venta::selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as cantidad, SUM(total) as total')
            ->whereBetween('fecha_venta', [$inicioMes, now()])
            ->where('estado', 'pagada')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $ventas,
        ]);
    }
}