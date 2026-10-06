<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\Presupuesto;
use Illuminate\Http\Request;

class PresupuestoController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'mes'  => 'nullable|integer|between:1,12',
            'anio' => 'nullable|integer|min:2000|max:2100',
        ]);

        $mes  = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);
        $userId = auth()->id();

        $gastadoPorCategoria = Gasto::where('user_id', $userId)
            ->whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->get(['categoria', 'monto'])
            ->groupBy(fn ($g) => $this->normalizar($g->categoria))
            ->map(fn ($items) => (float) $items->sum('monto'));

        $presupuestos = Presupuesto::where('user_id', $userId)
            ->orderBy('categoria')
            ->get()
            ->map(function ($p) use ($gastadoPorCategoria) {
                $limite  = (float) $p->monto_limite;
                $gastado = (float) $gastadoPorCategoria->get($this->normalizar($p->categoria), 0);

                return [
                    'id'           => $p->id,
                    'categoria'    => $p->categoria,
                    'monto_limite' => $limite,
                    'gastado'      => $gastado,
                    'restante'     => $limite - $gastado,
                    'porcentaje'   => $limite > 0 ? round($gastado / $limite * 100, 1) : 0,
                ];
            })
            ->values();

        return response()->json([
            'message' => 'Lista de presupuestos obtenida correctamente.',
            'data' => $presupuestos
        ], 200);
    }

    public function categorias()
    {
        $categorias = Gasto::where('user_id', auth()->id())
            ->whereNotNull('categoria')
            ->pluck('categoria')
            ->map(fn ($c) => trim($c))
            ->filter()
            ->unique(fn ($c) => mb_strtolower($c))
            ->sort()
            ->values();

        return response()->json(['data' => $categorias], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria'    => 'required|string|max:100',
            'monto_limite' => 'required|numeric|min:0.01|max:99999999',
        ]);
        $data['categoria'] = trim($data['categoria']);

        if ($this->existeCategoria($data['categoria'])) {
            return response()->json([
                'message' => 'Ya tienes un presupuesto para esa categoría.',
            ], 422);
        }

        $presupuesto = Presupuesto::create($data + ['user_id' => auth()->id()]);

        return response()->json([
            'message' => 'Presupuesto registrado correctamente.',
            'data' => $presupuesto
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $presupuesto = Presupuesto::where('user_id', auth()->id())->find($id);

        if (!$presupuesto) {
            return response()->json([
                'message' => 'Presupuesto no encontrado o no autorizado.',
            ], 404);
        }

        $data = $request->validate([
            'categoria'    => 'required|string|max:100',
            'monto_limite' => 'required|numeric|min:0.01|max:99999999',
        ]);
        $data['categoria'] = trim($data['categoria']);

        if ($this->existeCategoria($data['categoria'], $presupuesto->id)) {
            return response()->json([
                'message' => 'Ya tienes un presupuesto para esa categoría.',
            ], 422);
        }

        $presupuesto->update($data);

        return response()->json([
            'message' => 'Presupuesto actualizado correctamente.',
            'data' => $presupuesto
        ], 200);
    }

    public function destroy($id)
    {
        $presupuesto = Presupuesto::where('user_id', auth()->id())->find($id);

        if (!$presupuesto) {
            return response()->json([
                'message' => 'Presupuesto no encontrado o no autorizado.',
            ], 404);
        }

        $presupuesto->delete();

        return response()->json([
            'message' => 'Presupuesto eliminado correctamente.',
        ], 200);
    }

    private function normalizar(?string $categoria): string
    {
        return mb_strtolower(trim((string) $categoria));
    }

    private function existeCategoria(string $categoria, ?int $exceptId = null): bool
    {
        return Presupuesto::where('user_id', auth()->id())
            ->whereRaw('LOWER(TRIM(categoria)) = ?', [$this->normalizar($categoria)])
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }
}
