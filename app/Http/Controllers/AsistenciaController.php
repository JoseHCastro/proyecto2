<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AsistenciaController extends Controller
{
    public function index()
    {
        return Inertia::render('Asistencias/Registrar');
    }

    public function registrar(Request $request)
    {
        try {
            Log::info('Iniciando registro de asistencia', ['request' => $request->all()]);
            
            $request->validate([
                'email' => 'required|email',
            ]);

            $email = $request->email;
            Log::info('Email validado', ['email' => $email]);
            
            // Buscar al usuario por email
            $usuario = User::where('email', $email)->first();

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'tipo' => 'no_encontrado',
                    'mensaje' => 'Usuario no encontrado',
                ]);
            }

            // Verificar si es un cliente
            if (!$usuario->hasRole('Cliente')) {
                return response()->json([
                    'success' => false,
                    'tipo' => 'no_cliente',
                    'mensaje' => 'El usuario no es un cliente',
                ]);
            }

            // Verificar si ya marcó asistencia hoy
            $hoy = Carbon::today();
            $asistenciaHoy = Asistencia::where('socio_id', $usuario->id)
                ->whereDate('asistio_en', $hoy)
                ->first();

            if ($asistenciaHoy) {
                return response()->json([
                    'success' => false,
                    'tipo' => 'ya_marco',
                    'mensaje' => 'Ya marcaste asistencia hoy',
                    'nombre' => $usuario->name,
                    'hora' => $asistenciaHoy->asistio_en->format('H:i'),
                ]);
            }

            // Verificar si tiene suscripción activa
            $suscripcion = $usuario->suscripciones()
                ->with('paquete.membresia')
                ->orderBy('created_at', 'desc')
                ->first();

            Log::info('Suscripción encontrada', [
                'tiene_suscripcion' => $suscripcion ? 'sí' : 'no',
                'estado' => $suscripcion ? $suscripcion->estado : null
            ]);

            if (!$suscripcion) {
                return response()->json([
                    'success' => false,
                    'tipo' => 'sin_suscripcion',
                    'mensaje' => 'No tienes ninguna suscripción',
                    'nombre' => $usuario->name,
                ]);
            }

            // Verificar el estado de la suscripción
            if ($suscripcion->estado !== 'activo') {
                $estados = [
                    'inactivo' => 'Inactivo',
                    'suspendido' => 'Suspendido',
                    'vencido' => 'Vencido',
                    'cancelado' => 'Cancelado',
                ];
                
                return response()->json([
                    'success' => false,
                    'tipo' => 'suscripcion_' . $suscripcion->estado,
                    'mensaje' => 'Tu suscripción está en estado: ' . ($estados[$suscripcion->estado] ?? ucfirst($suscripcion->estado)),
                    'nombre' => $usuario->name,
                ]);
            }

            // Registrar asistencia solo si tiene suscripción activa
            $asistencia = Asistencia::create([
                'socio_id' => $usuario->id,
                'asistio_en' => now(),
            ]);

            return response()->json([
                'success' => true,
                'tipo' => 'registrado',
                'mensaje' => 'Asistencia registrada correctamente',
                'nombre' => $usuario->name,
                'suscripcion' => [
                    'paquete' => $suscripcion->paquete->nombre ?? 'Paquete',
                    'membresia' => $suscripcion->paquete->membresia->nombre ?? 'Membresía',
                    'fecha_fin' => $suscripcion->fecha_fin->format('d/m/Y'),
                ],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al registrar asistencia: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'tipo' => 'error',
                'mensaje' => 'Error al procesar la solicitud',
            ], 500);
        }
    }
}
