<?php
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\VentaController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\CompraController;
use App\Http\Controllers\Api\MovimientoInventarioController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\ProveedorController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Rutas públicas
    Route::post('/auth/login', [LoginController::class, 'login']);

    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/auth/logout', [LogoutController::class, 'logout']);
        Route::get('/auth/profile', [ProfileController::class, 'show']);
        Route::put('/auth/profile', [ProfileController::class, 'update']);

        // Solo admin
        Route::middleware('role:admin')->group(function () {
            Route::post('/auth/register', [RegisterController::class, 'register']);
            Route::get('/users', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'store']);
            Route::put('/users/{user}', [UserController::class, 'update']);
            Route::delete('/users/{user}', [UserController::class, 'destroy']);
        });

        // Catálogos (admin y farmacéutico)
        Route::middleware('role:admin,farmaceutico')->group(function () {
            Route::apiResource('categorias', CategoriaController::class);
            Route::apiResource('proveedores', ProveedorController::class);
            Route::apiResource('productos', ProductoController::class);
            Route::apiResource('clientes', ClienteController::class);
        });

        // Compras e inventario (admin y farmacéutico)
        Route::middleware('role:admin,farmaceutico')->group(function () {
            Route::apiResource('compras', CompraController::class);
            Route::get('movimientos-inventario', [MovimientoInventarioController::class, 'index']);
            Route::post('movimientos-inventario', [MovimientoInventarioController::class, 'store']);
            Route::get('movimientos-inventario/{movimientoInventario}', [MovimientoInventarioController::class, 'show']);
        });

        // Ventas (todos los roles autenticados)
        Route::middleware('role:admin,farmaceutico,cajero')->group(function () {
            Route::get('ventas', [VentaController::class, 'index']);
            Route::post('ventas', [VentaController::class, 'store']);
            Route::get('ventas/{venta}', [VentaController::class, 'show']);
            Route::post('ventas/{venta}/anular', [VentaController::class, 'anular']);
        });

                // Dashboard y reportes (solo admin y farmacéutico)
        Route::middleware('role:admin,farmaceutico')->group(function () {
            // Dashboard
            Route::get('dashboard/resumen', [DashboardController::class, 'resumen']);
            Route::get('dashboard/top-productos/{cantidad?}', [DashboardController::class, 'topProductos']);
            Route::get('dashboard/ventas-por-dia', [DashboardController::class, 'ventasPorDia']);

            // Reportes
            Route::get('reportes/ventas', [ReporteController::class, 'ventas']);
            Route::get('reportes/compras', [ReporteController::class, 'compras']);
            Route::get('reportes/inventario', [ReporteController::class, 'inventario']);
            Route::get('reportes/stock-bajo', [ReporteController::class, 'stockBajo']);
            Route::get('reportes/productos-vencidos', [ReporteController::class, 'productosVencidos']);
        });

    });
});