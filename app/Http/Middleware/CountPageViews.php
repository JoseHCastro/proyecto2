<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Vista;
use Illuminate\Support\Facades\Log;

class CountPageViews
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo contar peticiones GET
        if ($request->isMethod('GET')) {

            $url = $request->path();

            // Ignorar rutas de assets, debugbar, api, etc.
            if (!$this->shouldIgnore($url)) {
                try {
                    // Normalizar URL (ej: '/' es 'home')
                    $urlToStore = $url === '/' ? '/' : '/' . $url;

                    // Buscar o crear registro y aumentar contador
                    $vista = Vista::firstOrCreate(
                        ['url' => $urlToStore],
                        ['count' => 0]
                    );

                    $vista->increment('count');

                } catch (\Exception $e) {
                    // No detener la ejecución si falla el contador
                    Log::error('Error contando visita: ' . $e->getMessage());
                }
            }
        }

        return $next($request);
    }

    protected function shouldIgnore($url)
    {
        $ignoredPatterns = [
            'api/*',
            'build/*',
            'assets/*',
            'storage/*',
            'favicon.ico',
            'robots.txt',
            '_ignition/*',
            'sanctum/*',
            'livewire/*',
            'telescope/*',
        ];

        foreach ($ignoredPatterns as $pattern) {
            if (\Illuminate\Support\Str::is($pattern, $url)) {
                return true;
            }
        }

        return false;
    }
}
