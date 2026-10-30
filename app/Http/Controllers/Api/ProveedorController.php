<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Http\Resources\ProveedorResource;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::query();

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('ruc', 'like', '%' . $request->search . '%');
            });
        }

        $proveedores = $query->orderBy('nombre')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => ProveedorResource::collection($proveedores),
            'meta' => [
                'total' => $proveedores->total(),
                'per_page' => $proveedores->perPage(),
                'current_page' => $proveedores->currentPage(),
                'last_page' => $proveedores->lastPage(),
            ],
        ]);
    }

    public function store(StoreProveedorRequest $request)
    {
        $proveedor = Proveedor::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Proveedor creado exitosamente.',
            'data' => new ProveedorResource($proveedor),
        ], 201);
    }

    public function show(Proveedor $proveedor)
    {
        return response()->json([
            'success' => true,
            'data' => new ProveedorResource($proveedor),
        ]);
    }

    public function update(UpdateProveedorRequest $request, Proveedor $proveedor)
    {
        $proveedor->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Proveedor actualizado exitosamente.',
            'data' => new ProveedorResource($proveedor),
        ]);
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Proveedor eliminado exitosamente.',
        ]);
    }
}