<?php

namespace App\Http\Controllers;

use App\Models\Rutina;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RutinaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Rutina::with(['socio', 'instructor']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('ejercicio', 'like', "%{$search}%")
                ->orWhereHas('socio', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('instructor', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        $rutinas = $query->orderBy('creada_en', 'desc')->paginate(10)->withQueryString();

        return Inertia::render('Rutinas/Index', [
            'rutinas' => $rutinas,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $socios = User::role('Cliente')->orderBy('name', 'asc')->get();
        $instructores = User::role('Instructor')->orderBy('name', 'asc')->get();

        return Inertia::render('Rutinas/Create', [
            'socios' => $socios,
            'instructores' => $instructores,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'socio_id' => 'required|exists:users,id',
            'instructor_id' => 'required|exists:users,id',
            'ejercicio' => 'required|string|max:200',
            'series' => 'required|integer|min:1',
            'repeticiones' => 'required|integer|min:1',
        ]);

        $validated['creada_en'] = now()->toDateString();

        Rutina::create($validated);

        return redirect()->route('rutinas.index')->with('success', 'Rutina creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Rutina $rutina)
    {
        $rutina->load(['socio', 'instructor']);

        return Inertia::render('Rutinas/Show', [
            'rutina' => $rutina,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rutina $rutina)
    {
        $rutina->load(['socio', 'instructor']);
        
        $socios = User::role('Cliente')->orderBy('name', 'asc')->get();
        $instructores = User::role('Instructor')->orderBy('name', 'asc')->get();

        return Inertia::render('Rutinas/Edit', [
            'rutina' => $rutina,
            'socios' => $socios,
            'instructores' => $instructores,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rutina $rutina)
    {
        $validated = $request->validate([
            'socio_id' => 'required|exists:users,id',
            'instructor_id' => 'required|exists:users,id',
            'ejercicio' => 'required|string|max:200',
            'series' => 'required|integer|min:1',
            'repeticiones' => 'required|integer|min:1',
        ]);

        $rutina->update($validated);

        return redirect()->route('rutinas.index')->with('success', 'Rutina actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rutina $rutina)
    {
        $rutina->delete();

        return redirect()->route('rutinas.index')->with('success', 'Rutina eliminada exitosamente.');
    }
}
