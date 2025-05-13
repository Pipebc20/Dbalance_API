<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IngresoController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'categoria' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0|max:99999999',
            'fecha' => ['required', 'date', function ($attribute, $value, $fail) {
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $fail('El formato de fecha debe ser YYYY-MM-DD');
                }
            }],
            'descripcion' => 'nullable|string',
        ]);

        $ingreso = new Ingreso();
        $ingreso->user_id = auth()->id();
        $ingreso->fill($validatedData);
        $ingreso->save();

        return response()->json([
            'message' => 'Ingreso registrado correctamente.',
            'data' => $ingreso
        ], 201);
    }

    public function index()
    {
        $ingresos = Ingreso::where('user_id', auth()->id())
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->orderBy('id') // Ordenar por ID (puedes cambiar a 'fecha' si prefieres)
            ->get();

        // Asignar un número de orden dinámico por usuario
        $ingresosConOrden = $ingresos->map(function ($ingreso, $index) {
            $ingreso->numero_orden = $index + 1;
            return $ingreso;
        });

        return response()->json([
            'message' => 'Lista de ingresos obtenida correctamente.',
            'data' => $ingresosConOrden
        ], 200);
    }

    public function destroy($id)
    {
        $ingreso = Ingreso::where('user_id', auth()->id())->find($id);

        if (!$ingreso) {
            return response()->json([
                'message' => 'Ingreso no encontrado o no autorizado.',
            ], 404);
        }

        $ingreso->delete();

        return response()->json([
            'message' => 'Ingreso eliminado correctamente.',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $ingreso = Ingreso::where('user_id', auth()->id())->find($id);

        if (!$ingreso) {
            return response()->json([
                'message' => 'Ingreso no encontrado o no autorizado.',
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
        $ingreso = Ingreso::where('user_id', auth()->id())->find($id);

        if (!$ingreso) {
            return response()->json([
                'message' => 'Ingreso no encontrado o no autorizado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Ingreso obtenido correctamente.',
            'data' => $ingreso
        ], 200);
    }
}
