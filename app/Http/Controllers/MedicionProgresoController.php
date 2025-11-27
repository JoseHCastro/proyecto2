<?php

namespace App\Http\Controllers;

use App\Models\MedicionProgreso;
use Illuminate\Http\Request;

class MedicionProgresoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'socio_id' => 'required|exists:users,id',
            'peso_kg' => 'required|numeric|min:1',
            'estatura_cm' => 'required|numeric|min:1',
            'grasa_corporal' => 'nullable|numeric|min:0|max:100',
            'notas' => 'nullable|string|max:500',
        ]);

        $validated['medido_en'] = now();

        MedicionProgreso::create($validated);

        return back()->with('success', 'Medición de progreso registrada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicionProgreso $medicion_progreso)
    {
        $medicion_progreso->delete();

        return back()->with('success', 'Medición eliminada exitosamente.');
    }
}
