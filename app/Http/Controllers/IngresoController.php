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

    public function index(Request $request)
{
    $request->validate([
        'mes'  => 'nullable|integer|between:1,12',
        'anio' => 'nullable|integer|min:2000|max:2100',
    ]);

    $query = Ingreso::where('user_id', auth()->id())
        ->whereNotNull('categoria')
        ->where('categoria', '!=', '');

    if ($request->filled('mes')) {
        $query->whereMonth('fecha', $request->mes);
    }
    if ($request->filled('anio')) {
        $query->whereYear('fecha', $request->anio);
    }

    $ingresos = $query->orderBy('id')->get();

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
