<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('numero_documento', 'like', '%' . $request->search . '%');
            });
        }

        $clientes = $query->orderBy('nombre')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => ClienteResource::collection($clientes),
            'meta' => [
                'total' => $clientes->total(),
                'per_page' => $clientes->perPage(),
                'current_page' => $clientes->currentPage(),
                'last_page' => $clientes->lastPage(),
            ],
        ]);
    }

    public function store(StoreClienteRequest $request)
    {
        $cliente = Cliente::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cliente creado exitosamente.',
            'data' => new ClienteResource($cliente),
        ], 201);
    }

    public function show(Cliente $cliente)
    {
        return response()->json([
            'success' => true,
            'data' => new ClienteResource($cliente),
        ]);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado exitosamente.',
            'data' => new ClienteResource($cliente),
        ]);
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cliente eliminado exitosamente.',
        ]);
    }
}