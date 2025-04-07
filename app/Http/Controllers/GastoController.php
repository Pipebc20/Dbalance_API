<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class GastoController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'categoria' => 'required|string|max:255',
            'monto' => 'required|numeric',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string',
        ]);

        $gasto = Gasto::create([
            'user_id' => auth()->id(), // 🔹 Asigna el usuario autenticado
            'categoria' => $validatedData['categoria'],
            'monto' => $validatedData['monto'],
            'fecha' => $validatedData['fecha'],
            'descripcion' => $validatedData['descripcion'] ?? null,
        ]);

        return response()->json([
            'message' => 'Gasto registrado correctamente.',
            'data' => $gasto
        ], 201);
    }

    public function index()
    {
        $gastos = Gasto::all();

        return response()->json([
            'message' => 'Lista de gastos obtenida correctamente.',
            'data' => $gastos
        ], 200);
    }

    public function destroy($id)
    {
        $gasto = Gasto::find($id);

        if (!$gasto) {
            return response()->json([
                'message' => 'Gasto no encontrado.',
            ], 404);
        }
        $gasto->delete();

        return response()->json([
            'message' => 'Gasto eliminado correctamente.',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $gasto = Gasto::find($id);

        if (!$gasto) {
            return response()->json([
                'message' => 'Gasto no encontrado.',
            ], 404);
        }

        $validatedData = $request->validate([
            'categoria' => 'required|string|max:255',
            'monto' => 'required|numeric',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string',
        ]);

        $gasto->update($validatedData);

        return response()->json([
            'message' => 'Gasto actualizado correctamente.',
            'data' => $gasto
        ], 200);
    }

    public function show($id)
    {
        $gasto = Gasto::find($id);

        if (!$gasto) {
            return response()->json([
                'message' => 'Gasto no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Gasto obtenido correctamente.',
            'data' => $gasto
        ], 200);
    }
}
