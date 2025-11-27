<?php

namespace App\Observers;

use App\Models\User;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Solo generar QR para clientes
        if ($user->hasRole('Cliente') && !$user->url_qr) {
            $this->generarQr($user);
        }
    }

    /**
     * Genera el código QR para el usuario
     */
    private function generarQr(User $user): void
    {
        try {
            // Generar QR con el email del cliente como PNG
            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new ImagickImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCode = $writer->writeString($user->email);

            // Guardar en storage/app/public/qr
            $filename = 'qr_' . md5($user->email) . '.png';
            Storage::disk('public')->put('qr/' . $filename, $qrCode);

            // Actualizar usuario con la URL del QR
            $user->updateQuietly([
                'url_qr' => 'qr/' . $filename,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar QR para usuario: ' . $e->getMessage());
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
