<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Horario::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('hora_inicio', 'like', "%{$search}%")
                    ->orWhere('hora_fin', 'like', "%{$search}%");
            });
        }

        $horarios = $query->orderBy('hora_inicio', 'asc')->paginate(10)->withQueryString();

        return Inertia::render('Horarios/Index', [
            'horarios' => $horarios,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Horarios/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dia_semana' => 'required|array|min:1',
            'dia_semana.*' => 'integer|min:1|max:6',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        // Ordenar el array de días antes de guardar
        sort($validated['dia_semana']);

        Horario::create($validated);

        return redirect()->route('horarios.index')->with('success', 'Horario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Horario $horario)
    {
        return Inertia::render('Horarios/Show', [
            'horario' => $horario,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Horario $horario)
    {
        return Inertia::render('Horarios/Edit', [
            'horario' => [
                'id' => $horario->id,
                'dia_semana' => $horario->dia_semana,
                'hora_inicio' => substr($horario->hora_inicio, 0, 5),
                'hora_fin' => substr($horario->hora_fin, 0, 5),
                'created_at' => $horario->created_at,
                'updated_at' => $horario->updated_at,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Horario $horario)
    {
        $validated = $request->validate([
            'dia_semana' => 'required|array|min:1',
            'dia_semana.*' => 'integer|min:1|max:6',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        // Ordenar el array de días antes de guardar
        sort($validated['dia_semana']);

        // Reemplazar completamente los valores (sync)
        $horario->dia_semana = $validated['dia_semana'];
        $horario->hora_inicio = $validated['hora_inicio'];
        $horario->hora_fin = $validated['hora_fin'];
        $horario->save();

        return redirect()->route('horarios.index')->with('success', 'Horario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Horario $horario)
    {
        $horario->delete();

        return redirect()->route('horarios.index')->with('success', 'Horario eliminado exitosamente.');
    }
}
