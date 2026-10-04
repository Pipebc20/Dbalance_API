<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GastoController extends Controller
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

        $gasto = new Gasto();
        $gasto->user_id = auth()->id();
        $gasto->fill($validatedData);
        $gasto->save();

        return response()->json([
            'message' => 'Gasto registrado correctamente.',
            'data' => $gasto
        ], 201);
    }

    public function index(Request $request)
{
    $request->validate([
        'mes'  => 'nullable|integer|between:1,12',
        'anio' => 'nullable|integer|min:2000|max:2100',
    ]);

    $query = Gasto::where('user_id', auth()->id());

    if ($request->filled('mes')) {
        $query->whereMonth('fecha', $request->mes);
    }
    if ($request->filled('anio')) {
        $query->whereYear('fecha', $request->anio);
    }

    $gastos = $query->orderBy('id')->get();

    // Asignar un número de orden dinámico por usuario
    $gastosConOrden = $gastos->map(function ($gasto, $index) {
        $gasto->numero_orden = $index + 1;
        return $gasto;
    });

    return response()->json([
        'message' => 'Lista de gastos obtenida correctamente.',
        'data' => $gastosConOrden
    ], 200);
}

    public function destroy($id)
    {
        $gasto = Gasto::where('user_id', auth()->id())->find($id);

        if (!$gasto) {
            return response()->json([
                'message' => 'Gasto no encontrado o no autorizado.',
            ], 404);
        }

        $gasto->delete();

        return response()->json([
            'message' => 'Gasto eliminado correctamente.',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $gasto = Gasto::where('user_id', auth()->id())->find($id);

        if (!$gasto) {
            return response()->json([
                'message' => 'Gasto no encontrado o no autorizado.',
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
        $gasto = Gasto::where('user_id', auth()->id())->find($id);

        if (!$gasto) {
            return response()->json([
                'message' => 'Gasto no encontrado o no autorizado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Gasto obtenido correctamente.',
            'data' => $gasto
        ], 200);
    }
}
