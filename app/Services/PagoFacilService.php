<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PagoFacilService
{
    // URL base por defecto (se actualizará dinámicamente si es necesario)
    protected $baseUrl = 'https://masterqr.pagofacil.com.bo/api/services/v2';

    public function __construct()
    {
        $this->baseUrl = config('services.pagofacil.url', 'https://masterqr.pagofacil.com.bo/api/services/v2');
    }

    /**
     * Autenticación para obtener el Token
     * Prueba múltiples URLs y métodos hasta tener éxito.
     */
    public function login()
    {
        $serviceToken = config('services.pagofacil.service_token');
        $secretToken = config('services.pagofacil.secret_token');

        // Lista de URLs posibles para LOGIN
        $urls = [
            'https://serviciostigomoney.pagofacil.com.bo/api/servicio', // Antigua/Estándar (Suele funcionar para login)
            'https://masterqr.pagofacil.com.bo/api/services/v2',       // Nueva V2
            $this->baseUrl // La configurada
        ];

        $urls = array_unique($urls);

        foreach ($urls as $url) {
            // Intento 1: Enviar como JSON en el BODY
            try {
                $response = Http::withOptions(['verify' => false])->post($url . '/login', [
                    'tcTokenService' => $serviceToken,
                    'tcTokenSecret' => $secretToken,
                ]);

                if ($response->successful() && $response->json('error') === 0) {
                    // Logueamos qué URL funcionó para referencia
                    Log::info("PagoFácil Login exitoso en: $url (Body JSON)");
                    return ['success' => true, 'token' => $response->json('values.accessToken')];
                }
            } catch (\Exception $e) {
            }

            // Intento 2: Enviar en HEADERS
            try {
                $response = Http::withOptions(['verify' => false])->withHeaders([
                    'tcTokenService' => $serviceToken,
                    'tcTokenSecret' => $secretToken,
                ])->post($url . '/login');

                if ($response->successful() && $response->json('error') === 0) {
                    Log::info("PagoFácil Login exitoso en: $url (Headers)");
                    return ['success' => true, 'token' => $response->json('values.accessToken')];
                }
            } catch (\Exception $e) {
            }
        }

        Log::error('PagoFácil Login falló en todas las combinaciones.');
        return ['success' => false, 'message' => 'No se pudo autenticar. Verifica tus credenciales.'];
    }

    /**
     * Generar QR
     */
    public function generarQR($datos)
    {
        $loginResult = $this->login();

        if (!$loginResult['success']) {
            return ['success' => false, 'message' => $loginResult['message']];
        }

        $token = $loginResult['token'];

        // Para generar QR, SIEMPRE usamos la URL V2 que sabemos que tiene el servicio QR BCP (ID 4)
        $qrUrl = 'https://masterqr.pagofacil.com.bo/api/services/v2';

        try {
            $response = Http::withOptions(['verify' => false])->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post($qrUrl . '/generate-qr', $datos);

            if ($response->successful() && $response->json('error') === 0) {
                return ['success' => true, 'data' => $response->json('values')];
            }

            Log::error('PagoFácil Generar QR Error. Status: ' . $response->status());
            Log::error('PagoFácil Response Body: ' . $response->body());

            $mensajeError = $response->json('message') ?? 'Error desconocido de PagoFácil';
            return ['success' => false, 'message' => $mensajeError];

        } catch (\Exception $e) {
            Log::error('PagoFácil Generar QR Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()];
        }
    }

    /**
     * Consultar Transacción
     */
    public function consultarTransaccion($pagofacilTransactionId)
    {
        $loginResult = $this->login();

        if (!$loginResult['success']) {
            return null;
        }

        $token = $loginResult['token'];
        $queryUrl = 'https://masterqr.pagofacil.com.bo/api/services/v2';

        try {
            $response = Http::withOptions(['verify' => false])->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->post($queryUrl . '/query-transaction', [
                        'pagofacilTransactionId' => $pagofacilTransactionId
                    ]);

            if ($response->successful() && $response->json('error') === 0) {
                return $response->json('values');
            }

            return null;
        } catch (\Exception $e) {
            Log::error('PagoFácil Consultar Exception: ' . $e->getMessage());
            return null;
        }
    }
}