<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class IngresoController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'categoria' => 'required|string|max:255',
            'monto' => 'required|numeric',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string',
        ]);

        $ingreso = Ingreso::create([
            'user_id' => auth()->id(), // 🔹 Asigna el usuario autenticado
            'categoria' => $validatedData['categoria'],
            'monto' => $validatedData['monto'],
            'fecha' => $validatedData['fecha'],
            'descripcion' => $validatedData['descripcion'] ?? null,
        ]);

        return response()->json([
            'message' => 'Ingreso registrado correctamente.',
            'data' => $ingreso
        ], 201);
    }

    public function index()
    {
        $ingresos = Ingreso::all();

        return response()->json([
            'message' => 'Lista de ingresos obtenida correctamente.',
            'data' => $ingresos
        ], 200);
    }

    public function destroy($id)
    {
        $ingreso = Ingreso::find($id);

        if (!$ingreso) {
            return response()->json([
                'message' => 'Ingreso no encontrado.',
            ], 404);
        }

        $ingreso->delete();

        return response()->json([
            'message' => 'Ingreso eliminado correctamente.',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $ingreso = Ingreso::find($id);

        if (!$ingreso) {
            return response()->json([
                'message' => 'Ingreso no encontrado.',
            ], 404);
        }

        $validatedData = $request->validate([
            'categoria' => 'required|string|max:255',
            'monto' => 'required|numeric',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string',
        ]);

        $ingreso->update($validatedData);

        return response()->json([
            'message' => 'Ingreso actualizado correctamente.',
            'data' => $ingreso
        ], 200);
    }

    public function show($id)
    {
        $ingreso = Ingreso::find($id);

        if (!$ingreso) {
            return response()->json([
                'message' => 'Ingreso no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Ingreso obtenido correctamente.',
            'data' => $ingreso
        ], 200);
    }
}
