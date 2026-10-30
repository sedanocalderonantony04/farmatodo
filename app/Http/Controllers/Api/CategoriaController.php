<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Http\Resources\CategoriaResource;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Categoria::query();

        // Filtro por estado
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        // Búsqueda por nombre
        if ($request->has('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $categorias = $query->orderBy('nombre')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => CategoriaResource::collection($categorias),
            'meta' => [
                'total' => $categorias->total(),
                'per_page' => $categorias->perPage(),
                'current_page' => $categorias->currentPage(),
                'last_page' => $categorias->lastPage(),
            ],
        ]);
    }

    public function store(StoreCategoriaRequest $request)
    {
        $categoria = Categoria::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada exitosamente.',
            'data' => new CategoriaResource($categoria),
        ], 201);
    }

    public function show(Categoria $categoria)
    {
        return response()->json([
            'success' => true,
            'data' => new CategoriaResource($categoria),
        ]);
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada exitosamente.',
            'data' => new CategoriaResource($categoria),
        ]);
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada exitosamente.',
        ]);
    }
}