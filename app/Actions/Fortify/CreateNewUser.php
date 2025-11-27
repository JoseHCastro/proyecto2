<?php

namespace App\Actions\Fortify;

use App\Models\User;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'telefono' => ['required', 'string', 'max:20'],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'telefono' => $input['telefono'],
            'password' => $input['password'],
            'estado' => 'activo', // Estado activo por defecto
        ]);

        // Asignar rol de Cliente automáticamente
        $user->assignRole('Cliente');

        // Generar QR automáticamente
        $this->generarQr($user);

        return $user;
    }

    /**
     * Genera el código QR para el usuario
     */
    private function generarQr(User $user): void
    {
        try {
            // Generar QR con el email del cliente como SVG (más compatible)
            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCode = $writer->writeString($user->email);

            // Guardar en storage/app/public/qr
            $filename = 'qr_' . md5($user->email) . '.svg';
            Storage::disk('public')->put('qr/' . $filename, $qrCode);

            // Actualizar usuario con la URL del QR
            $user->update([
                'url_qr' => 'qr/' . $filename,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar QR para usuario: ' . $e->getMessage());
        }
    }
}
