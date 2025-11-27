<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DisciplinaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Disciplina::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
        }

        $disciplinas = $query->orderBy('nombre', 'asc')->paginate(10)->withQueryString();

        return Inertia::render('Disciplinas/Index', [
            'disciplinas' => $disciplinas,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Disciplinas/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:disciplinas,nombre',
            'descripcion' => 'nullable|string|max:500',
        ]);

        Disciplina::create($validated);

        return redirect()->route('disciplinas.index')->with('success', 'Disciplina creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Disciplina $disciplina)
    {
        return Inertia::render('Disciplinas/Show', [
            'disciplina' => $disciplina,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Disciplina $disciplina)
    {
        return Inertia::render('Disciplinas/Edit', [
            'disciplina' => $disciplina,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Disciplina $disciplina)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:disciplinas,nombre,' . $disciplina->id,
            'descripcion' => 'nullable|string|max:500',
        ]);

        $disciplina->update($validated);

        return redirect()->route('disciplinas.index')->with('success', 'Disciplina actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Disciplina $disciplina)
    {
        $disciplina->delete();

        return redirect()->route('disciplinas.index')->with('success', 'Disciplina eliminada exitosamente.');
    }
}
