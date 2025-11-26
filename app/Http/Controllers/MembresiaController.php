<?php

namespace App\Http\Controllers;

use App\Models\Membresia;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MembresiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Membresia::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->has('activo')) {
            $activo = $request->input('activo');
            if ($activo !== '') {
                $query->where('activo', $activo === 'true' ? true : false);
            }
        }

        $membresias = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return Inertia::render('Membresias/Index', [
            'membresias' => $membresias,
            'filters' => $request->only(['search', 'activo']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Membresias/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'duracion_dias' => 'required|integer|min:1',
        ]);

        $validated['activo'] = true;

        Membresia::create($validated);

        return redirect()->route('membresias.index')->with('success', 'Membresía creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Membresia $membresia)
    {
        return Inertia::render('Membresias/Show', [
            'membresia' => $membresia,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Membresia $membresia)
    {
        return Inertia::render('Membresias/Edit', [
            'membresia' => $membresia,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Membresia $membresia)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'duracion_dias' => 'required|integer|min:1',
            'activo' => 'required|boolean',
        ]);

        $membresia->update($validated);

        return redirect()->route('membresias.index')->with('success', 'Membresía actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Membresia $membresia)
    {
        $membresia->delete();

        return redirect()->route('membresias.index')->with('success', 'Membresía eliminada exitosamente.');
    }
}
