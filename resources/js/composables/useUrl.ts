/**
 * Composable para manejar URLs en despliegues con subdirectorio
 * 
 * En producción (subdirectorio): /inf513/grupo23sc/proyecto2/public/membresias
 * En desarrollo (raíz): /membresias
 */

import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { getBaseUrl } from '@/utils/baseUrl';

export function useUrl() {
    const page = usePage();
    
    /**
     * Obtiene el base path del subdirectorio (si existe)
     */
    const basePath = computed(() => {
        // Primero intentar desde el estado global
        const globalBase = getBaseUrl();
        if (globalBase) return globalBase;
        
        // Fallback a la prop de la página
        const appUrl = (page.props as any).app_url || '';
        if (!appUrl || appUrl.includes('localhost') || appUrl.includes('127.0.0.1')) {
            return '';
        }
        try {
            const url = new URL(appUrl);
            return url.pathname.endsWith('/') ? url.pathname.slice(0, -1) : url.pathname;
        } catch {
            return '';
        }
    });

    /**
     * Construye una URL con el prefijo del subdirectorio si es necesario
     * @param path - La ruta relativa (ej: '/membresias/create')
     * @returns La URL completa con el prefijo si aplica
     */
    const url = (path: string): string => {
        if (!basePath.value) return path;
        
        // Si ya tiene el prefijo, no agregarlo de nuevo
        if (path.startsWith(basePath.value)) return path;
        
        // Asegurarse de que el path comience con /
        const normalizedPath = path.startsWith('/') ? path : `/${path}`;
        
        return `${basePath.value}${normalizedPath}`;
    };

    return {
        basePath,
        url,
    };
}
