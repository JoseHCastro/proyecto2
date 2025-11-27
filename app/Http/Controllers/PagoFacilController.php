<?php

namespace App\Http\Controllers;

use App\Models\PagoFacil;
use App\Models\Pago;
use App\Services\PagoFacilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PagoFacilController extends Controller
{
    protected $pagoFacilService;

    public function __construct(PagoFacilService $pagoFacilService)
    {
        $this->pagoFacilService = $pagoFacilService;
    }

    public function index()
    {
        // Listar todos los pagos (para ver los generados a clientes)
        // En producción deberías filtrar por roles (ej: si es admin ve todo, si es cliente solo los suyos)
        $pagos = PagoFacil::with('user') // Cargar relación usuario si existe para mostrar nombre
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Pagos/Index', [
            'pagos' => $pagos
        ]);
    }

    public function generarQr(Request $request)
    {
        $user = Auth::user();

        // Generar un ID único para la transacción
        $pedidoId = 'PED-' . time() . '-' . $user->id;

        // Datos fijos según requerimiento (Monto de prueba 1.00 Bs)
        $monto = 1.00;
        $telefono = '71199056'; // Teléfono fijo solicitado

        // Preparar datos para PagoFácil
        $datos = [
            "paymentMethod" => 4, // ID 4 = QR BCP
            "clientName" => $user->name,
            "documentType" => 1, // CI
            "documentId" => $user->ci ?? '0000000',
            "phoneNumber" => "71199056",
            "email" => $user->email,
            "paymentNumber" => $pedidoId,
            "amount" => $monto,
            "currency" => 2, // 1=USD, 2=BOB (Bolivianos)
            "clientCode" => (string) ($user->id ?? '0'),
            "callbackUrl" => "https://www.tecnoweb.org.bo/inf513/grupo23sc/proyecto2/payment/callback",
            "orderDetail" => [
                [
                    "serial" => 1,
                    "product" => "Servicio Gym",
                    "quantity" => 1,
                    "price" => $monto,
                    "discount" => 0,
                    "total" => $monto
                ]
            ]
        ];

        $resultado = $this->pagoFacilService->generarQR($datos);

        if ($resultado['success']) {
            $valores = $resultado['data'];

            // Guardar en Base de Datos
            PagoFacil::create([
                'user_id' => $user->id,
                'pedido_id' => $pedidoId,
                'pagofacil_transaction_id' => $valores['transactionId'],
                'monto' => $monto,
                'qr_image' => $valores['qrBase64'],
                'estado' => 1, // Pendiente
                'nombre_cliente' => $user->name,
                'ci_cliente' => $user->ci,
                'telefono_cliente' => $telefono,
                'email_cliente' => $user->email,
            ]);

            return redirect()->back()->with('qr_data', $valores);
        } else {
            return redirect()->back()->withErrors(['error' => $resultado['message']]);
        }
    }

    /**
     * Generar QR para una cuota específica de suscripción
     */
    public function generarQrCuota(Pago $pago)
    {
        $user = $pago->usuario; // Asumiendo relación 'usuario' en modelo Pago

        // Generar un ID de pedido único para esta transacción
        $pedidoId = "CUOTA-{$pago->id}-" . time();

        // MONTO FIJO PARA PRUEBAS (1.00 Bs)
        $monto = 1.00;

        // Datos para PagoFácil
        $datos = [
            "paymentMethod" => 4, // QR BCP
            "clientName" => $user->name,
            "documentType" => 1, // CI
            "documentId" => $user->ci ?? '0000000',
            "phoneNumber" => "71199056", // Fijo por ahora
            "email" => $user->email,
            "paymentNumber" => $pedidoId,
            "amount" => $monto,
            "currency" => 2, // BOB
            "clientCode" => (string) $user->id,
            "callbackUrl" => "https://www.tecnoweb.org.bo/inf513/grupo23sc/proyecto2/payment/callback",
            "orderDetail" => [
                [
                    "serial" => 1,
                    "product" => $pago->motivo,
                    "quantity" => 1,
                    "price" => $monto,
                    "discount" => 0,
                    "total" => $monto
                ]
            ]
        ];

        $resultado = $this->pagoFacilService->generarQR($datos);

        if (!$resultado['success']) {
            return redirect()->back()->with('error', 'Error al generar QR: ' . $resultado['message']);
        }

        $data = $resultado['data'];

        // Guardar en base de datos
        PagoFacil::create([
            'user_id' => $user->id,
            'pedido_id' => $pedidoId,
            'pagofacil_transaction_id' => $data['transactionId'],
            'monto' => $monto, // Guardamos 1.00 Bs para que coincida con el QR
            'qr_image' => $data['qrBase64'],
            'estado' => 1, // Pendiente
            'nombre_cliente' => $user->name,
            'ci_cliente' => $user->ci ?? '0000000',
            'telefono_cliente' => "71199056",
            'email_cliente' => $user->email,
        ]);

        // Redirigir a la vista de Pagos QR con un mensaje de éxito y los datos del QR
        return redirect()->route('pagofacil.index')
            ->with('success', 'QR generado correctamente para la cuota.')
            ->with('qr_data', $data);
    }

    public function callback(Request $request)
    {
        Log::info('PagoFácil Callback recibido', $request->all());

        try {
            $pedidoId = $request->input('PedidoID');
            $estado = $request->input('Estado'); // "2" es Pagado

            // Buscar la transacción
            $pagoFacil = PagoFacil::where('pedido_id', $pedidoId)->first();

            if ($pagoFacil) {
                $nuevoEstado = $pagoFacil->estado;

                if ($estado == 2) {
                    $nuevoEstado = 2; // Pagado
                    $pagoFacil->fecha_pago = now();
                } elseif ($estado == 4) {
                    $nuevoEstado = 4; // Anulado
                } elseif ($estado == 5) {
                    $nuevoEstado = 5; // Revisión (Considerado pagado para el negocio)
                    $pagoFacil->fecha_pago = now(); // Asignamos fecha pago también
                }

                $pagoFacil->estado = $nuevoEstado;
                $pagoFacil->save();

                // Lógica adicional: Si es el pago de una cuota, actualizar el modelo Pago
                // Se actualiza si es Pagado (2) o Revisión (5)
                if (($nuevoEstado == 2 || $nuevoEstado == 5) && str_starts_with($pagoFacil->pedido_id, 'CUOTA-')) {
                    // Extraer ID del pago: CUOTA-{id}-{timestamp}
                    $parts = explode('-', $pagoFacil->pedido_id);
                    if (isset($parts[1])) {
                        $pagoId = $parts[1];
                        $pagoCuota = Pago::find($pagoId);
                        if ($pagoCuota) {
                            $pagoCuota->update([
                                'estado' => 'pagada',
                                'metodo' => 'QR',
                                'fecha' => now(),
                            ]);
                            Log::info("Cuota #{$pagoId} marcada como pagada vía QR (Estado: {$nuevoEstado}).");
                        }
                    }
                }

                return response()->json([
                    "error" => 0,
                    "status" => 1,
                    "message" => "Estado actualizado correctamente",
                    "values" => true
                ]);
            }

            return response()->json([
                "error" => 1,
                "status" => 0,
                "message" => "Pedido no encontrado",
                "values" => false
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error en Callback: ' . $e->getMessage());
            return response()->json([
                "error" => 1,
                "status" => 0,
                "message" => "Error interno",
                "values" => false
            ], 500);
        }
    }

    public function consultarEstado($id)
    {
        $pagoFacil = PagoFacil::findOrFail($id);

        // Consultar a la API
        $info = $this->pagoFacilService->consultarTransaccion($pagoFacil->pagofacil_transaction_id);

        if ($info) {
            // Actualizar estado localmente
            // paymentStatus: 2 (Pagado)
            $estadoApi = $info['paymentStatus']; // Ojo: verificar si la API devuelve string o int

            // Mapear estado API a nuestro estado
            // API: 1=Pendiente, 2=Pagado
            // BD: 1=Pendiente, 2=Pagado

            if ($pagoFacil->estado != $estadoApi) {
                $pagoFacil->estado = $estadoApi;

                // Si es Pagado (2) o Revisión (5), marcamos fecha de pago y actualizamos cuota
                if (($estadoApi == 2 || $estadoApi == 5) && !$pagoFacil->fecha_pago) {
                    $pagoFacil->fecha_pago = now();

                    // Actualizar cuota si corresponde
                    if (str_starts_with($pagoFacil->pedido_id, 'CUOTA-')) {
                        $parts = explode('-', $pagoFacil->pedido_id);
                        if (isset($parts[1])) {
                            $pagoId = $parts[1];
                            $pagoCuota = Pago::find($pagoId);
                            if ($pagoCuota) {
                                $pagoCuota->update([
                                    'estado' => 'pagada',
                                    'metodo' => 'QR',
                                    'fecha' => now(),
                                ]);
                            }
                        }
                    }
                }
                $pagoFacil->save();
            }

            return response()->json(['status' => 'success', 'data' => $info]);
        }

        return response()->json(['status' => 'error', 'message' => 'No se pudo consultar'], 500);
    }
}
