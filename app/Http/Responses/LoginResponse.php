<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = auth()->user();
        
        // Determinar la URL de redirección según el rol
        $redirectUrl = $this->getRedirectUrlForUser($user);

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 200)
            : redirect()->intended($redirectUrl);
    }

    /**
     * Obtener la URL de redirección según el rol del usuario.
     */
    private function getRedirectUrlForUser($user): string
    {
        // Cliente -> Mi QR
        if ($user->hasRole('Cliente')) {
            return '/mi-qr';
        }
        
        // Instructor -> Gestionar rutinas
        if ($user->hasRole('Instructor')) {
            return '/rutinas';
        }
        
        // Propietario o Secretaria -> Dashboard
        if ($user->hasRole('Propietario') || $user->hasRole('Secretaria')) {
            return '/dashboard';
        }
        
        // Por defecto -> Dashboard
        return '/dashboard';
    }
}
