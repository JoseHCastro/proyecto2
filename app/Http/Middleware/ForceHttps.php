<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     * Redirige todas las peticiones HTTP a HTTPS en producción.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo forzar HTTPS en producción (tecnoweb.org.bo)
        if (str_contains(config('app.url'), 'tecnoweb.org.bo')) {
            // Verificar si la petición NO es segura
            if (!$request->secure() && !$this->isSecureRequest($request)) {
                return redirect()->secure($request->getRequestUri(), 301);
            }
        }

        return $next($request);
    }

    /**
     * Verificar si la petición es segura (considera proxies y headers)
     */
    protected function isSecureRequest(Request $request): bool
    {
        // Verificar headers comunes de proxies reversos
        if ($request->header('X-Forwarded-Proto') === 'https') {
            return true;
        }

        if ($request->header('X-Forwarded-Ssl') === 'on') {
            return true;
        }

        if ($request->header('X-Url-Scheme') === 'https') {
            return true;
        }

        return false;
    }
}
