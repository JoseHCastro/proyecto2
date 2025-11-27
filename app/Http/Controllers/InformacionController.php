<?php

namespace App\Http\Controllers;

use App\Models\Informacion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InformacionController extends Controller
{
    public function index()
    {
        $informacion = Informacion::first();
        
        return Inertia::render('Informacion/Index', [
            'informacion' => $informacion,
        ]);
    }

    public function edit()
    {
        $informacion = Informacion::first();
        
        if (!$informacion) {
            $informacion = Informacion::create([
                'nombre' => 'Elevation Gym',
                'direccion' => 'B/ San Lorenzo entre 4to y 5to anillo esquina Calle Cunumicita y calle El Transnochado',
                'telefono' => '',
                'correo' => '',
            ]);
        }
        
        return Inertia::render('Informacion/Edit', [
            'informacion' => $informacion,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
        ]);

        $informacion = Informacion::first();
        
        if (!$informacion) {
            $informacion = Informacion::create($validated);
        } else {
            $informacion->update($validated);
        }

        return redirect()->route('informacion.index')
            ->with('success', 'Información actualizada correctamente');
    }
}
