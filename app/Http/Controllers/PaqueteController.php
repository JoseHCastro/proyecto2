<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use App\Models\Membresia;
use App\Models\Sesion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaqueteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Paquete::with(['membresia', 'sesiones.disciplina']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhereHas('membresia', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
        }

        $paquetes = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return Inertia::render('Paquetes/Index', [
            'paquetes' => $paquetes,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $membresias = Membresia::where('activo', true)->orderBy('nombre', 'asc')->get();
        $sesiones = Sesion::with(['disciplina', 'horario'])->get();

        return Inertia::render('Paquetes/Create', [
            'membresias' => $membresias,
            'sesiones' => $sesiones,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:paquetes,nombre',
            'descripcion' => 'nullable|string|max:500',
            'precio' => 'required|numeric|min:0.01|regex:/^\d+(\.\d{1,2})?$/',
            'membresia_id' => 'required|exists:membresias,id',
            'sesiones' => 'required|array|min:1',
            'sesiones.*' => 'exists:sesiones,id',
        ], [
            'precio.regex' => 'El precio debe tener máximo 2 decimales.',
            'sesiones.required' => 'Debes seleccionar al menos una sesión.',
            'sesiones.min' => 'Debes seleccionar al menos una sesión.',
        ]);

        $validated['activo'] = true;

        $paquete = Paquete::create($validated);
        $paquete->sesiones()->attach($validated['sesiones']);

        return redirect()->route('paquetes.index')->with('success', 'Paquete creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paquete $paquete)
    {
        $paquete->load(['membresia', 'sesiones.disciplina', 'sesiones.horario', 'sesiones.instructor']);

        return Inertia::render('Paquetes/Show', [
            'paquete' => $paquete,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paquete $paquete)
    {
        $paquete->load(['membresia', 'sesiones']);
        
        $membresias = Membresia::where('activo', true)->orderBy('nombre', 'asc')->get();
        $sesiones = Sesion::with(['disciplina', 'horario'])->get();

        return Inertia::render('Paquetes/Edit', [
            'paquete' => $paquete,
            'membresias' => $membresias,
            'sesiones' => $sesiones,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paquete $paquete)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:paquetes,nombre,' . $paquete->id,
            'descripcion' => 'nullable|string|max:500',
            'precio' => 'required|numeric|min:0.01|regex:/^\d+(\.\d{1,2})?$/',
            'membresia_id' => 'required|exists:membresias,id',
            'activo' => 'required|boolean',
            'sesiones' => 'required|array|min:1',
            'sesiones.*' => 'exists:sesiones,id',
        ], [
            'precio.regex' => 'El precio debe tener máximo 2 decimales.',
            'sesiones.required' => 'Debes seleccionar al menos una sesión.',
            'sesiones.min' => 'Debes seleccionar al menos una sesión.',
        ]);

        $paquete->update($validated);
        $paquete->sesiones()->sync($validated['sesiones']);

        return redirect()->route('paquetes.index')->with('success', 'Paquete actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paquete $paquete)
    {
        $paquete->delete();

        return redirect()->route('paquetes.index')->with('success', 'Paquete eliminado exitosamente.');
    }
}
