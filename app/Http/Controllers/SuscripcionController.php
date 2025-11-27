<?php

namespace App\Http\Controllers;

use App\Models\Suscripcion;
use App\Models\Paquete;
use App\Models\Pago;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class SuscripcionController extends Controller
{
    public function index()
    {
        $suscripciones = Suscripcion::with(['usuario', 'paquete.membresia'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Suscripciones/Index', [
            'suscripciones' => $suscripciones,
        ]);
    }

    public function create()
    {
        $clientes = User::role('Cliente')->get();
        $paquetes = Paquete::with('membresia')->where('activo', true)->get();

        return Inertia::render('Suscripciones/Create', [
            'clientes' => $clientes,
            'paquetes' => $paquetes,
        ]);
    }

    public function store(Request $request)
    {
        // Obtener el paquete para validar las cuotas
        $paquete = Paquete::with('membresia')->findOrFail($request->paquete_id);
        $maxCuotas = floor($paquete->membresia->duracion_dias / 2);

        $validated = $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'paquete_id' => 'required|exists:paquetes,id',
            'renovacion_automatica' => 'boolean',
            'cuotas' => "required|integer|min:1|max:{$maxCuotas}",
        ], [
            'cuotas.max' => "El número de cuotas no puede ser mayor a {$maxCuotas} (duración en días / 2)",
        ]);
        
        // Calcular fechas
        $fechaInicio = Carbon::today();
        $fechaFin = $fechaInicio->copy()->addDays($paquete->membresia->duracion_dias);

        // Crear suscripción
        $suscripcion = Suscripcion::create([
            'usuario_id' => $validated['usuario_id'],
            'paquete_id' => $validated['paquete_id'],
            'estado' => 'activo',
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'renovacion_automatica' => $validated['renovacion_automatica'] ?? false,
            'cuotas' => $validated['cuotas'],
        ]);

        // Crear pagos usando el método helper
        $this->crearPagos($suscripcion, $paquete, $validated['cuotas'], $fechaInicio);

        return redirect()->route('suscripciones.index')
            ->with('success', 'Suscripción creada correctamente con ' . $validated['cuotas'] . ' cuota(s)');
    }

    private function crearPagos($suscripcion, $paquete, $cuotas, $fechaInicio)
    {
        // Calcular monto por cuota
        $montoPorCuota = $paquete->precio / $cuotas;
        
        // Calcular días entre pagos
        $diasEntrePagos = floor($paquete->membresia->duracion_dias / $cuotas);

        // Crear motivo
        $motivo = "Cuota {numeroCuota} - {$paquete->nombre} - {$paquete->membresia->nombre} - Elevation Gym";

        // Crear pagos (cuotas)
        for ($i = 0; $i < $cuotas; $i++) {
            // Calcular fecha de vencimiento para cada cuota
            // Primera cuota vence mañana, las siguientes según los días entre pagos
            $fechaVence = $fechaInicio->copy()->addDays(1 + ($i * $diasEntrePagos));

            Pago::create([
                'usuario_id' => $suscripcion->usuario_id,
                'suscripcion_id' => $suscripcion->id,
                'motivo' => str_replace('{numeroCuota}', $i + 1, $motivo),
                'monto' => round($montoPorCuota, 2),
                'metodo' => null,
                'estado' => 'impaga',
                'fecha' => null,
                'vence' => $fechaVence,
            ]);
        }
    }

    public function show(Suscripcion $suscripcione)
    {
        $suscripcione->load(['usuario', 'paquete.membresia', 'pagos' => function ($query) {
            $query->orderBy('vence', 'asc');
        }]);

        return Inertia::render('Suscripciones/Show', [
            'suscripcion' => $suscripcione,
        ]);
    }

    public function edit(Suscripcion $suscripcione)
    {
        $suscripcione->load('paquete.membresia', 'usuario');
        $clientes = User::role('Cliente')->get();
        $paquetes = Paquete::with('membresia')->where('activo', true)->get();

        return Inertia::render('Suscripciones/Edit', [
            'suscripcion' => $suscripcione,
            'clientes' => $clientes,
            'paquetes' => $paquetes,
        ]);
    }

    public function update(Request $request, Suscripcion $suscripcione)
    {
        $validated = $request->validate([
            'estado' => 'required|in:activo,inactivo,suspendido,vencida,cancelada',
            'renovacion_automatica' => 'boolean',
        ]);

        // Si se cancela la suscripción, cancelar pagos impagos
        if ($validated['estado'] === 'cancelada') {
            $suscripcione->pagos()->where('estado', 'impaga')->update(['estado' => 'cancelado']);
        }

        $suscripcione->update($validated);

        return redirect()->route('suscripciones.index')
            ->with('success', 'Suscripción actualizada correctamente');
    }

    public function destroy(Suscripcion $suscripcione)
    {
        // No se puede eliminar, solo cancelar
        return redirect()->route('suscripciones.index')
            ->with('error', 'No se puede eliminar una suscripción, solo se puede cancelar');
    }

    public function pagarCuota(Request $request, $pagoId)
    {
        $pago = Pago::with('suscripcion')->findOrFail($pagoId);

        // Verificar que no haya pagos impagos anteriores
        $pagosAnterioresImpagos = Pago::where('suscripcion_id', $pago->suscripcion_id)
            ->where('vence', '<', $pago->vence)
            ->where('estado', 'impaga')
            ->count();

        if ($pagosAnterioresImpagos > 0) {
            return back()->with('error', 'Debe pagar las cuotas anteriores primero');
        }

        $validated = $request->validate([
            'metodo' => 'required|in:efectivo,QR,tarjeta',
        ]);

        $pago->update([
            'metodo' => $validated['metodo'],
            'estado' => 'pagada',
            'fecha' => Carbon::now(),
        ]);

        return back()->with('success', 'Pago registrado correctamente');
    }
}
