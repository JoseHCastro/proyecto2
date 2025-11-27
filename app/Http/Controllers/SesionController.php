<?php

namespace App\Http\Controllers;

use App\Models\Sesion;
use App\Models\Disciplina;
use App\Models\Horario;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SesionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sesion::with(['disciplina', 'horario', 'instructor']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('disciplina', function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%");
            })
            ->orWhereHas('instructor', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $sesiones = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return Inertia::render('Sesiones/Index', [
            'sesiones' => $sesiones,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $disciplinas = Disciplina::orderBy('nombre', 'asc')->get();
        $horarios = Horario::orderBy('hora_inicio', 'asc')->get();
        $instructores = User::role('Instructor')->orderBy('name', 'asc')->get();

        return Inertia::render('Sesiones/Create', [
            'disciplinas' => $disciplinas,
            'horarios' => $horarios,
            'instructores' => $instructores,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'horario_id' => 'required|exists:horarios,id',
            'instructor_id' => 'required|exists:users,id',
        ]);

        // Verificar que no exista una sesión con la misma combinación
        $exists = Sesion::where('disciplina_id', $validated['disciplina_id'])
            ->where('horario_id', $validated['horario_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'disciplina_id' => 'Ya existe una sesión con esta disciplina y horario.'
            ]);
        }

        Sesion::create($validated);

        return redirect()->route('sesiones.index')->with('success', 'Sesión creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sesion $sesione)
    {
        $sesione->load(['disciplina', 'horario', 'instructor']);

        return Inertia::render('Sesiones/Show', [
            'sesion' => $sesione,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sesion $sesione)
    {
        $sesione->load(['disciplina', 'horario', 'instructor']);
        
        $disciplinas = Disciplina::orderBy('nombre', 'asc')->get();
        $horarios = Horario::orderBy('hora_inicio', 'asc')->get();
        $instructores = User::role('Instructor')->orderBy('name', 'asc')->get();

        return Inertia::render('Sesiones/Edit', [
            'sesion' => $sesione,
            'disciplinas' => $disciplinas,
            'horarios' => $horarios,
            'instructores' => $instructores,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sesion $sesione)
    {
        $validated = $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'horario_id' => 'required|exists:horarios,id',
            'instructor_id' => 'required|exists:users,id',
        ]);

        // Verificar que no exista otra sesión con la misma combinación
        $exists = Sesion::where('disciplina_id', $validated['disciplina_id'])
            ->where('horario_id', $validated['horario_id'])
            ->where('id', '!=', $sesione->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'disciplina_id' => 'Ya existe una sesión con esta disciplina y horario.'
            ]);
        }

        $sesione->update($validated);

        return redirect()->route('sesiones.index')->with('success', 'Sesión actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sesion $sesione)
    {
        $sesione->delete();

        return redirect()->route('sesiones.index')->with('success', 'Sesión eliminada exitosamente.');
    }
}
